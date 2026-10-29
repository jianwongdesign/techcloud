<?php
// Add the native enquiry form to Contact without changing any other page content.
if (!defined('WP_CLI') || !WP_CLI) exit;
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) WP_CLI::error('Local installations only.');
if (get_option('tc_contact_form_added')) return;
$page = get_page_by_path('contact');
if (!$page) WP_CLI::error('Contact page is missing.');
update_post_meta($page->ID, '_tc_before_contact_form', $page->post_content);
$content = '<!-- wp:group {"className":"contact-layout"} --><div class="wp-block-group contact-layout"><!-- wp:shortcode -->[techcloud_contact]<!-- /wp:shortcode --><!-- wp:shortcode -->[techcloud_contact_form]<!-- /wp:shortcode --></div><!-- /wp:group -->';
$result = wp_update_post(['ID' => $page->ID, 'post_content' => $content], true);
if (is_wp_error($result)) WP_CLI::error($result->get_error_message());
update_option('tc_contact_form_added', gmdate('c'));
WP_CLI::success('Contact page form added. Previous content retained.');
