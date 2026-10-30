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
    wp_enqueue_style('techcloud-rich', get_theme_file_uri('assets/rich.css'), ['techcloud'], filemtime(get_theme_file_path('assets/rich.css')));
    wp_enqueue_script('techcloud', get_theme_file_uri('assets/site.js'), [], filemtime(get_theme_file_path('assets/site.js')), ['strategy' => 'defer', 'in_footer' => true]);
});
// Use the brand mark until a site icon is supplied through WordPress settings.
add_action('wp_head', function () {
    if (!has_site_icon()) {
        $url = add_query_arg('v', filemtime(get_theme_file_path('assets/favicon.svg')), get_theme_file_uri('assets/favicon.svg'));
        echo '<link rel="icon" type="image/svg+xml" sizes="any" href="' . esc_url($url) . '">' . "\n";
    }
});
function tc_setting($key) {
    $defaults = ['email' => 'hello@techcloud.sg', 'phone' => '+65 6049 6060', 'address' => '629 Aljunied Road, Cititech Industrial Building, #03-03, Singapore 389838', 'shop_url' => ''];
    return get_theme_mod('tc_' . $key, $defaults[$key] ?? '');
}
add_action('customize_register', function ($customizer) {
    $customizer->add_section('tc_business', ['title' => 'TechCloud business details', 'priority' => 30]);
    foreach (['email' => 'Contact email', 'phone' => 'Phone number', 'address' => 'Office address', 'shop_url' => 'Shop destination (full URL or /shop/)'] as $key => $label) {
        $sanitize = $key === 'email' ? 'sanitize_email' : ($key === 'shop_url' ? 'esc_url_raw' : 'sanitize_text_field');
        $customizer->add_setting('tc_' . $key, ['default' => tc_setting($key), 'sanitize_callback' => $sanitize]);
        $customizer->add_control('tc_' . $key, ['section' => 'tc_business', 'label' => $label, 'type' => 'text']);
    }
});
function tc_arrow() { return '<svg class="arrow-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14"/></svg>'; }
function tc_icon($name) {
    $paths = [
        'server' => '<rect x="4" y="3" width="24" height="11" rx="2"/><rect x="4" y="18" width="24" height="11" rx="2"/><path d="M9 8h.01M9 23h.01M15 8h8M15 23h8"/>',
        'support' => '<path d="M5 17v-3a11 11 0 0 1 22 0v3M27 22v3a4 4 0 0 1-4 4h-6"/><rect x="3" y="14" width="6" height="10" rx="2"/><rect x="23" y="14" width="6" height="10" rx="2"/>',
        'people' => '<circle cx="12" cy="9" r="5"/><path d="M3 28v-4a9 9 0 0 1 18 0v4M23 4a5 5 0 0 1 0 10M26 19a7 7 0 0 1 3 6v3"/>',
        'project' => '<rect x="5" y="5" width="22" height="24" rx="2"/><path d="M11 3v5M21 3v5M5 12h22M11 21l3 3 7-7"/>',
        'network' => '<rect x="11" y="2" width="10" height="8" rx="1"/><rect x="2" y="22" width="10" height="8" rx="1"/><rect x="20" y="22" width="10" height="8" rx="1"/><path d="M16 10v6M7 22v-6h18v6"/>',
    ];
    return '<svg class="service-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? $paths['server']) . '</svg>';
}
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
    $heading = is_page($parent->ID) ? 'h2' : 'h3';
    $pages = get_pages(['parent' => $parent->ID, 'sort_column' => 'menu_order,post_title', 'number' => absint($attributes['limit'])]);
    if (!$pages) return '';
    $showcase_id = wp_unique_id('services-');
    ob_start();
    echo '<div class="services-index" data-service-showcase><nav class="service-menu" aria-label="IT services">';
    foreach ($pages as $i => $service) {
        echo '<a class="service-choice" id="' . esc_attr($showcase_id . '-choice-' . $i) . '" data-service-choice href="' . esc_url(get_permalink($service)) . '"><span>' . esc_html(get_post_meta($service->ID, '_tc_service_label', true) ?: $service->post_title) . '</span>' . tc_arrow() . '</a>';
    }
    echo '</nav><div class="service-panels">';
    foreach ($pages as $i => $service) {
        echo '<article class="service-panel" id="' . esc_attr($showcase_id . '-panel-' . $i) . '" data-service-panel' . ($i ? ' hidden' : '') . '>';
        echo tc_service_image($service);
        echo '<div class="service-summary"><' . $heading . '>' . esc_html(get_post_meta($service->ID, '_tc_service_label', true) ?: $service->post_title) . '</' . $heading . '><p>' . esc_html(get_post_meta($service->ID, '_tc_service_summary', true) ?: $service->post_excerpt) . '</p><a class="button button-light" href="' . esc_url(get_permalink($service)) . '">Explore service ' . tc_arrow() . '</a></div></article>';
    }
    echo '</div></div>'; return ob_get_clean();
}
add_shortcode('techcloud_services', 'tc_service_list');
function tc_service_image($service) {
    $images = [
        'managed-it-services' => 'managed-it-poster.webp',
        'end-user-it-support' => 'support-poster.webp',
        'it-staffing' => 'workplace.webp',
        'project-management' => 'infrastructure.webp',
        'it-installation-infrastructure' => 'network-poster.webp',
    ];
    $image = get_the_post_thumbnail($service->ID, 'large', ['loading' => 'lazy', 'decoding' => 'async', 'alt' => '']);
    if (!$image) {
        $asset = $images[$service->post_name] ?? 'infrastructure.webp';
        $image = '<img src="' . esc_url(get_theme_file_uri('assets/' . $asset)) . '" width="960" height="540" loading="lazy" decoding="async" alt="">';
    }
    return '<div class="service-visual">' . $image . '</div>';
}
function tc_case_image($size = 'large') {
    if (has_post_thumbnail()) return get_the_post_thumbnail(null, $size, ['loading' => 'lazy', 'decoding' => 'async']);
    $images = [
        'connected-workplace-example' => ['network-poster.webp', 'Illustrative connected network infrastructure'],
        'growing-team-support-example' => ['support-poster.webp', 'Illustrative IT support at a workstation'],
        'network-refresh-example' => ['infrastructure.webp', 'Illustrative server infrastructure'],
        'device-rollout-example' => ['workplace.webp', 'Illustrative office workstations'],
    ];
    // Real case studies require their own photograph; only demo posts use demo assets.
    if (!get_post_meta(get_the_ID(), '_tc_example', true)) return '';
    [$asset, $alt] = $images[get_post_field('post_name', get_the_ID())] ?? $images['connected-workplace-example'];
    return '<img src="' . esc_url(get_theme_file_uri('assets/' . $asset)) . '" width="960" height="540" loading="lazy" decoding="async" alt="' . esc_attr($alt) . '">';
}
function tc_case_card($heading = 'h3') {
    $heading = in_array($heading, ['h2', 'h3'], true) ? $heading : 'h3';
    echo '<a class="case-card" href="' . esc_url(get_permalink()) . '"><div class="case-image">';
    echo tc_case_image();
    echo '</div><div class="case-body"><' . $heading . '>' . esc_html(get_the_title()) . '</' . $heading . '><p>' . esc_html(get_the_excerpt()) . '</p>';
    if (get_post_meta(get_the_ID(), '_tc_example', true)) echo '<p class="case-disclosure">Illustrative example · not a client result</p>';
    echo '<span class="text-link">' . (get_post_meta(get_the_ID(), '_tc_example', true) ? 'Explore the example ' : 'Explore the story ') . tc_arrow() . '</span></div></a>';
}
function tc_case_list($attributes = []) {
    $attributes = shortcode_atts(['limit' => 2], $attributes);
    $query = new WP_Query(['post_type' => 'tc_case', 'posts_per_page' => max(1, absint($attributes['limit'])), 'post_status' => 'publish', 'orderby' => ['menu_order' => 'ASC', 'date' => 'DESC']]);
    ob_start();
    if (!$query->have_posts()) echo '<div class="empty-state"><h3>Good work deserves a proper story.</h3><p>Our case studies are being prepared. In the meantime, talk to us about your IT needs.</p><a class="text-link" href="' . esc_url(home_url('/contact/')) . '">Start a conversation ' . tc_arrow() . '</a></div>';
    else {
        echo '<div class="case-grid">';
        while ($query->have_posts()) {
            $query->the_post();
            tc_case_card();
        }
        echo '</div>';
        if (is_front_page()) echo '<a class="text-link case-all-link" href="' . esc_url(get_post_type_archive_link('tc_case')) . '">View all case studies ' . tc_arrow() . '</a>';
    }
    wp_reset_postdata(); return ob_get_clean();
}
add_shortcode('techcloud_cases', 'tc_case_list');
add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_post_type_archive('tc_case')) {
        $query->set('posts_per_page', 8);
        $query->set('orderby', ['menu_order' => 'ASC', 'date' => 'DESC']);
    }
});
function tc_cta() {
    // Retain the legacy shortcode for saved blocks; the footer now handles contact.
    return '';
}
add_shortcode('techcloud_cta', 'tc_cta');
add_shortcode('techcloud_contact', function () {
    $phone = tc_setting('phone');
    return '<div class="contact-details"><a href="mailto:' . esc_attr(tc_setting('email')) . '"><span class="eyebrow">EMAIL US</span><strong>' . esc_html(tc_setting('email')) . '</strong>' . tc_arrow() . '</a><a href="tel:' . esc_attr(preg_replace('/[^+0-9]/', '', $phone)) . '"><span class="eyebrow">CALL US</span><strong>' . esc_html($phone) . '</strong>' . tc_arrow() . '</a><div><span class="eyebrow">FIND US</span><p>' . esc_html(tc_setting('address')) . '</p></div></div>';
});

require_once get_theme_file_path('inc/seo.php');
