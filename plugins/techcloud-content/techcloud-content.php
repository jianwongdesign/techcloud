<?php
/**
 * Plugin Name: TechCloud Content
 * Description: Case studies and private contact enquiries for TechCloud's custom theme.
 * Version: 0.2.0
 */
defined('ABSPATH') || exit;
function tc_register_content() {
    register_post_type('tc_case', [
        'labels' => ['name' => 'Case Studies', 'singular_name' => 'Case Study', 'add_new_item' => 'Add Case Study'],
        'public' => true, 'show_in_rest' => true, 'has_archive' => 'case-studies',
        'rewrite' => ['slug' => 'case-studies', 'with_front' => false],
        'menu_icon' => 'dashicons-portfolio',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
    ]);
}
add_action('init', 'tc_register_content');
register_activation_hook(__FILE__, function () { tc_register_content(); flush_rewrite_rules(); });
register_deactivation_hook(__FILE__, function () { flush_rewrite_rules(); });
require_once __DIR__ . '/contact.php';
