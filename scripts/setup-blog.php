<?php
/** Explicit WP-CLI setup; loading the theme never changes site settings. */
if (!defined('WP_CLI') || !WP_CLI) exit;
$host = wp_parse_url(home_url(), PHP_URL_HOST);
if (!in_array($host, ['localhost', '127.0.0.1'], true) && !in_array('allow-live', $args ?? [], true)) {
    WP_CLI::error('Live setup requires a backup, reviewed existing URLs, and the explicit allow-live argument.');
}
$blog = get_page_by_path('blog');
if ($blog && ($blog->post_status !== 'publish' || $blog->ID === (int) get_option('page_on_front'))) {
    WP_CLI::error('The existing /blog/ page needs review before it can become the posts page.');
}
$existing_posts_page = (int) get_option('page_for_posts');
if ($existing_posts_page && (!$blog || $existing_posts_page !== $blog->ID)) {
    WP_CLI::error('A different posts page is already assigned. Review it before proceeding.');
}
if (get_option('show_on_front') !== 'page') {
    WP_CLI::error('This setup expects the existing TechCloud static homepage.');
}
if (!get_option('tc_blog_previous_settings')) {
    add_option('tc_blog_previous_settings', ['page_for_posts' => $existing_posts_page, 'permalink_structure' => get_option('permalink_structure'), 'blog_page_existed' => (bool) $blog], '', false);
}
if (!$blog) {
    $id = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Blog', 'post_name' => 'blog'], true);
    if (is_wp_error($id)) WP_CLI::error($id->get_error_message());
    $blog = get_post($id);
}
update_option('page_for_posts', $blog->ID);
global $wp_rewrite;
$wp_rewrite->set_permalink_structure('/blog/%postname%/');
flush_rewrite_rules();
WP_CLI::success('Blog configured at /blog/ with /blog/{post-slug}/ article URLs. No posts imported or replaced.');
