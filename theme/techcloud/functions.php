<?php
defined('ABSPATH') || exit;
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_editor_style('assets/editor.css');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus(['primary' => 'Primary navigation']);
    add_post_type_support('page', 'excerpt');
});
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('techcloud', get_theme_file_uri('assets/site.css'), [], filemtime(get_theme_file_path('assets/site.css')));
    wp_enqueue_script('techcloud', get_theme_file_uri('assets/site.js'), [], filemtime(get_theme_file_path('assets/site.js')), ['strategy' => 'defer', 'in_footer' => true]);
});
function tc_setting($key) {
    $defaults = ['email' => 'hello@techcloud.sg', 'phone' => '+65 6049 6060', 'address' => '629 Aljunied Road, Cititech Industrial Building, #03-03, Singapore 389838', 'shop_url' => '', 'cta_title' => 'Let’s make IT one less thing to worry about.', 'cta_text' => 'Tell us what your business needs. We’ll help you find a practical way forward.'];
    return get_theme_mod('tc_' . $key, $defaults[$key] ?? '');
}
add_action('customize_register', function ($customizer) {
    $customizer->add_section('tc_business', ['title' => 'TechCloud business details', 'priority' => 30]);
    foreach (['email' => 'Contact email', 'phone' => 'Phone number', 'address' => 'Office address', 'shop_url' => 'Shop destination (full URL or /shop/)', 'cta_title' => 'Contact banner heading', 'cta_text' => 'Contact banner description'] as $key => $label) {
        $sanitize = $key === 'email' ? 'sanitize_email' : ($key === 'shop_url' ? 'esc_url_raw' : 'sanitize_text_field');
        $customizer->add_setting('tc_' . $key, ['default' => tc_setting($key), 'sanitize_callback' => $sanitize]);
        $customizer->add_control('tc_' . $key, ['section' => 'tc_business', 'label' => $label, 'type' => 'text']);
    }
});
function tc_arrow() { return '<span aria-hidden="true">↗</span>'; }
function tc_shop_url() {
    $url = tc_setting('shop_url');
    if (!$url) return home_url('/shop/');
    return str_starts_with($url, '/') ? home_url($url) : $url;
}
function tc_default_menu() {
    $items = ['/' => 'Home', '/services/' => 'Services', '/case-studies/' => 'Case studies', '/contact/' => 'Contact'];
    echo '<ul class="nav-list">';
    foreach ($items as $path => $label) {
        $active = $path === '/' ? is_front_page() : ($path === '/case-studies/' ? (is_post_type_archive('tc_case') || is_singular('tc_case')) : is_page(trim($path, '/')));
        echo '<li><a ' . ($active ? 'aria-current="page" ' : '') . 'href="' . esc_url(home_url($path)) . '">' . esc_html($label) . '</a></li>';
    }
    echo '</ul>';
}
function tc_service_list($attributes = []) {
    $attributes = shortcode_atts(['limit' => 0], $attributes);
    $parent = get_page_by_path('services');
    if (!$parent) return '';
    $pages = get_pages(['parent' => $parent->ID, 'sort_column' => 'menu_order,post_title', 'number' => absint($attributes['limit'])]);
    ob_start(); echo '<div class="service-list">';
    foreach ($pages as $i => $service) {
        echo '<a class="service-row" href="' . esc_url(get_permalink($service)) . '"><span class="row-number">' . esc_html(sprintf('%02d', $i + 1)) . '</span><h3>' . esc_html($service->post_title) . '</h3><p>' . esc_html($service->post_excerpt) . '</p><span class="circle-arrow">' . tc_arrow() . '</span></a>';
    }
    echo '</div>'; return ob_get_clean();
}
add_shortcode('techcloud_services', 'tc_service_list');
function tc_case_list($attributes = []) {
    $attributes = shortcode_atts(['limit' => 2], $attributes);
    $query = new WP_Query(['post_type' => 'tc_case', 'posts_per_page' => max(1, absint($attributes['limit'])), 'post_status' => 'publish']);
    ob_start();
    if (!$query->have_posts()) echo '<div class="empty-state"><h3>Good work deserves a proper story.</h3><p>Our case studies are being prepared. In the meantime, talk to us about your IT needs.</p><a class="text-link" href="' . esc_url(home_url('/contact/')) . '">Start a conversation ' . tc_arrow() . '</a></div>';
    else {
        echo '<div class="case-grid">';
        while ($query->have_posts()) {
            $query->the_post();
            echo '<a class="case-card" href="' . esc_url(get_permalink()) . '"><div class="case-image">';
            if (has_post_thumbnail()) the_post_thumbnail('large', ['loading' => 'lazy']);
            else echo '<div class="case-graphic" aria-hidden="true"><i></i><i></i><i></i><i></i></div>';
            echo '</div><div class="case-caption"><div><span class="eyebrow">' . (get_post_meta(get_the_ID(), '_tc_example', true) ? 'Illustrative example · not a client result' : 'Case study') . '</span><h3>' . esc_html(get_the_title()) . '</h3></div>' . tc_arrow() . '</div><p>' . esc_html(get_the_excerpt()) . '</p></a>';
        }
        echo '</div>';
    }
    wp_reset_postdata(); return ob_get_clean();
}
add_shortcode('techcloud_cases', 'tc_case_list');
function tc_cta() {
    return '<section class="contact-banner"><div><span class="eyebrow">YOUR NEXT STEP</span><h2>' . esc_html(tc_setting('cta_title')) . '</h2><p>' . esc_html(tc_setting('cta_text')) . '</p></div><a class="button button-light" href="' . esc_url(home_url('/contact/')) . '">Let’s talk ' . tc_arrow() . '</a></section>';
}
add_shortcode('techcloud_cta', 'tc_cta');
add_shortcode('techcloud_contact', function () {
    $phone = tc_setting('phone');
    return '<div class="contact-details"><a href="mailto:' . esc_attr(tc_setting('email')) . '"><span class="eyebrow">EMAIL US</span><strong>' . esc_html(tc_setting('email')) . '</strong>' . tc_arrow() . '</a><a href="tel:' . esc_attr(preg_replace('/[^+0-9]/', '', $phone)) . '"><span class="eyebrow">CALL US</span><strong>' . esc_html($phone) . '</strong>' . tc_arrow() . '</a><div><span class="eyebrow">FIND US</span><p>' . esc_html(tc_setting('address')) . '</p></div></div>';
});
