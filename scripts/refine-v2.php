<?php
// One-time, local-only content refinement. Original content is retained in post meta.
if (!defined('WP_CLI') || !WP_CLI) exit;
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) WP_CLI::error('Local installations only.');
if (get_option('tc_refined_v2')) { WP_CLI::success('V2 content already installed.'); return; }
function tc_v2_clean($blocks) {
    $result = [];
    foreach ($blocks as $block) {
        if ($block['blockName'] === 'core/paragraph' && ($block['attrs']['className'] ?? '') === 'eyebrow') {
            $text = trim(wp_strip_all_tags($block['innerHTML']));
            if (!preg_match('/^0[123] /', $text) && !str_contains($text, 'ILLUSTRATIVE')) continue;
        }
        if ($block['innerBlocks']) {
            $old = $block['innerBlocks']; $children = []; $content = []; $i = 0;
            foreach ($block['innerContent'] as $part) {
                if ($part !== null) { $content[] = $part; continue; }
                $cleaned = tc_v2_clean([$old[$i++]]);
                if ($cleaned) { $children[] = $cleaned[0]; $content[] = null; }
            }
            $block['innerBlocks'] = $children; $block['innerContent'] = $content;
        }
        $result[] = $block;
    }
    return $result;
}
$home = (int) get_option('page_on_front');
$services = get_page_by_path('services'); $process = '';
foreach (parse_blocks($services->post_content) as $block) {
    if (str_contains(serialize_block($block), 'process-grid')) {
        $block = tc_v2_clean([$block])[0];
        $block['attrs']['className'] = 'section home-process';
        $block['innerContent'][0] = str_replace('wp-block-group section', 'wp-block-group section home-process', $block['innerContent'][0]);
        $process = serialize_block($block);
    }
}
foreach (get_posts(['post_type' => 'page', 'posts_per_page' => -1, 'post_status' => 'publish']) as $page) {
    if ($page->post_name === 'sample-page') continue;
    update_post_meta($page->ID, '_tc_content_before_v2', $page->post_content);
    $blocks = tc_v2_clean(parse_blocks($page->post_content)); $content = '';
    foreach ($blocks as $block) {
        if ($page->ID === $home && ($block['attrs']['className'] ?? '') === 'faq-section') $content .= $process;
        $content .= serialize_block($block);
    }
    if ($page->ID === $home) {
        $image = '<!-- wp:image {"sizeSlug":"large","className":"workplace-photo"} --><figure class="wp-block-image size-large workplace-photo"><img src="' . esc_url(get_theme_file_uri('assets/workplace.webp')) . '" alt="Illustrative modern office with connected workstations" width="1536" height="1024" loading="lazy"/></figure><!-- /wp:image -->';
        $content = str_replace('<div class="wp-block-group about-intro">', '<div class="wp-block-group about-intro">' . $image, $content);
    }
    wp_update_post(['ID' => $page->ID, 'post_content' => $content]);
}
update_option('tc_refined_v2', gmdate('c'));
WP_CLI::success('Richer native block content installed; original content retained.');
