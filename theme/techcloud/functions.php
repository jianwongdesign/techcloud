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
    $pages = get_pages(['parent' => $parent->ID, 'sort_column' => 'menu_order,post_title', 'number' => absint($attributes['limit'])]);
    ob_start(); echo '<div class="service-list">';
    foreach ($pages as $i => $service) {
        $icons = ['managed-it-services' => 'server', 'end-user-it-support' => 'support', 'it-staffing' => 'people', 'project-management' => 'project', 'it-installation-infrastructure' => 'network'];
        preg_match_all('/<li\b[^>]*>(.*?)<\/li>/s', $service->post_content, $scope);
        echo '<article class="service-row">';
        $clips = ['managed-it-services' => 'managed-it', 'end-user-it-support' => 'support', 'it-installation-infrastructure' => 'network'];
        if (isset($clips[$service->post_name])) echo tc_video($clips[$service->post_name], $service->post_title);
        echo '<a class="service-card-link" href="' . esc_url(get_permalink($service)) . '"><div class="service-card-top">' . tc_icon($icons[$service->post_name] ?? 'server') . '<span class="circle-arrow">' . tc_arrow() . '</span></div><h3>' . esc_html($service->post_title) . '</h3><p>' . esc_html($service->post_excerpt) . '</p>';
        if (!empty($scope[1])) {
            echo '<ul class="service-capabilities">';
            foreach (array_slice($scope[1], 0, 3) as $item) echo '<li>' . esc_html(wp_strip_all_tags($item)) . '</li>';
            echo '</ul>';
        }
        echo '<span class="service-explore">Explore service ' . tc_arrow() . '</span></a></article>';
    }
    echo '</div>'; return ob_get_clean();
}
add_shortcode('techcloud_services', 'tc_service_list');
function tc_video($name, $label) {
    if (!file_exists(get_theme_file_path('assets/' . $name . '.mp4'))) {
        $poster = file_exists(get_theme_file_path('assets/' . $name . '-poster.webp')) ? $name . '-poster.webp' : 'infrastructure.webp';
        return '<div class="service-media"><img src="' . esc_url(get_theme_file_uri('assets/' . $poster)) . '" width="960" height="540" loading="lazy" alt="Illustrative ' . esc_attr($label) . ' environment"></div>';
    }
    return '<div class="service-media"><video muted loop playsinline preload="none" poster="' . esc_url(get_theme_file_uri('assets/' . $name . '-poster.webp')) . '" data-src="' . esc_url(get_theme_file_uri('assets/' . $name . '.mp4')) . '" aria-label="Illustrative ' . esc_attr($label) . ' video"></video><button class="motion-control" type="button" hidden aria-label="Play ' . esc_attr($label) . ' video">Play video</button></div>';
}
function tc_case_card($heading = 'h3') {
    $heading = in_array($heading, ['h2', 'h3'], true) ? $heading : 'h3';
    echo '<a class="case-card" href="' . esc_url(get_permalink()) . '"><div class="case-image">';
    if (has_post_thumbnail()) the_post_thumbnail('large', ['loading' => 'lazy']);
    else echo '<img src="' . esc_url(get_theme_file_uri('assets/workplace.webp')) . '" width="1536" height="1024" loading="lazy" alt="Illustrative modern office workspace">';
    echo '</div><div class="case-body"><' . $heading . '>' . esc_html(get_the_title()) . '</' . $heading . '><p>' . esc_html(get_the_excerpt()) . '</p>';
    if (get_post_meta(get_the_ID(), '_tc_example', true)) echo '<p class="case-disclosure">Illustrative example · not a client result</p>';
    echo '<span class="text-link">Explore the story ' . tc_arrow() . '</span></div></a>';
}
function tc_case_list($attributes = []) {
    $attributes = shortcode_atts(['limit' => 2], $attributes);
    $query = new WP_Query(['post_type' => 'tc_case', 'posts_per_page' => max(1, absint($attributes['limit'])), 'post_status' => 'publish']);
    ob_start();
    if (!$query->have_posts()) echo '<div class="empty-state"><h3>Good work deserves a proper story.</h3><p>Our case studies are being prepared. In the meantime, talk to us about your IT needs.</p><a class="text-link" href="' . esc_url(home_url('/contact/')) . '">Start a conversation ' . tc_arrow() . '</a></div>';
    else {
        echo '<div class="case-grid">';
        while ($query->have_posts()) {
            $query->the_post();
            tc_case_card();
        }
        echo '</div>';
    }
    wp_reset_postdata(); return ob_get_clean();
}
add_shortcode('techcloud_cases', 'tc_case_list');
function tc_cta() {
    return '<section class="contact-banner"><div><h2>' . esc_html(tc_setting('cta_title')) . '</h2><p>' . esc_html(tc_setting('cta_text')) . '</p></div><a class="button button-light" href="' . esc_url(home_url('/contact/')) . '">Let’s talk ' . tc_arrow() . '</a></section>';
}
add_shortcode('techcloud_cta', 'tc_cta');
add_shortcode('techcloud_contact', function () {
    $phone = tc_setting('phone');
    return '<div class="contact-details"><a href="mailto:' . esc_attr(tc_setting('email')) . '"><span class="eyebrow">EMAIL US</span><strong>' . esc_html(tc_setting('email')) . '</strong>' . tc_arrow() . '</a><a href="tel:' . esc_attr(preg_replace('/[^+0-9]/', '', $phone)) . '"><span class="eyebrow">CALL US</span><strong>' . esc_html($phone) . '</strong>' . tc_arrow() . '</a><div><span class="eyebrow">FIND US</span><p>' . esc_html(tc_setting('address')) . '</p></div></div>';
});
