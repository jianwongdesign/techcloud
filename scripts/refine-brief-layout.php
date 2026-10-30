<?php
// Group existing native blocks for layout only. Keep every supplied word intact.
if (!defined('WP_CLI') || !WP_CLI) exit;
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) WP_CLI::error('Local installations only.');
if (get_option('tc_brief_layout_v1')) { WP_CLI::success('Client brief layout already refined.'); return; }

function tc_layout_group($class, $blocks) {
    return parse_blocks('<!-- wp:group {"className":"'.$class.'"} --><div class="wp-block-group '.$class.'">'.serialize_blocks($blocks).'</div><!-- /wp:group -->')[0];
}
function tc_layout_blocks($blocks) {
    foreach ($blocks as &$block) {
        if (($block['attrs']['className'] ?? '') === 'service-capability') {
            $children = array_values(array_filter($block['innerBlocks'], fn($child) => !empty($child['blockName'])));
            if (count($children) > 2) {
                $block = tc_layout_group('service-capability', [
                    tc_layout_group('service-capability-intro', array_slice($children, 0, 2)),
                    tc_layout_group('service-capability-features', array_slice($children, 2)),
                ]);
            } else {
                $block = tc_layout_group('service-capability service-capability--summary', $children);
            }
        } elseif (!empty($block['innerBlocks'])) {
            $block['innerBlocks'] = tc_layout_blocks($block['innerBlocks']);
        }
    }
    return $blocks;
}
foreach (['managed-it-services', 'end-user-it-support', 'it-staffing', 'it-installation-infrastructure'] as $slug) {
    $post = get_page_by_path('services/'.$slug);
    if (!$post || !get_post_meta($post->ID, '_tc_client_brief_source', true)) WP_CLI::error('Apply the client brief first.');
    $content = serialize_blocks(tc_layout_blocks(parse_blocks($post->post_content)));
    if (wp_strip_all_tags($content) !== wp_strip_all_tags($post->post_content)) WP_CLI::error('Content changed unexpectedly: '.$slug);
    if (!metadata_exists('post', $post->ID, '_tc_before_brief_layout')) update_post_meta($post->ID, '_tc_before_brief_layout', $post->post_content);
    $result = wp_update_post(['ID' => $post->ID, 'post_content' => $content], true);
    if (is_wp_error($result)) WP_CLI::error($result->get_error_message());
}
update_option('tc_brief_layout_v1', 1);
WP_CLI::success('Service scope blocks regrouped; supplied copy unchanged.');
