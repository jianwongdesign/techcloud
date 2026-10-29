<?php
// Restore the version preceding the editorial redesign. Local review installs only.
if (!defined('WP_CLI') || !WP_CLI) exit;
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) WP_CLI::error('Local installations only.');
if (!get_option('tc_refined_v3') || get_option('tc_restored_v3')) return;
$posts = get_posts(['post_type' => ['page', 'tc_case'], 'posts_per_page' => -1, 'post_status' => 'publish', 'meta_key' => '_tc_before_v3']);
foreach ($posts as $post) {
    $previous = get_post_meta($post->ID, '_tc_before_v3', true);
    if (!is_array($previous) || count($previous) !== 3) WP_CLI::error('Incomplete previous version for ' . $post->post_name);
    if (!metadata_exists('post', $post->ID, '_tc_reverted_v3')) update_post_meta($post->ID, '_tc_reverted_v3', array_intersect_key((array) $post, array_flip(['post_title', 'post_excerpt', 'post_content'])));
    $result = wp_update_post(wp_slash(array_merge(['ID' => $post->ID], $previous)), true);
    if (is_wp_error($result)) WP_CLI::error($result->get_error_message());
}
update_option('tc_restored_v3', gmdate('c'));
WP_CLI::success('Previous page titles, excerpts and native content restored. Editorial revision retained in metadata.');
