<?php
// Review-only policy drafts. Existing published client content is preserved.
if (!defined('WP_CLI') || !WP_CLI) exit;
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) WP_CLI::error('Local installations only.');
if (get_option('tc_policy_pages_added')) return;
$pages = [
    'terms-and-conditions' => ['Terms & Conditions', 'Information about this website and its use.', [
        'About this website' => 'This website introduces TechCloud Private Limited and its IT services. Information is provided for general enquiries. A service engagement, price, delivery date or support commitment is confirmed separately in an agreed quotation or contract.',
        'Content and examples' => 'The case studies labelled illustrative are proposed scenarios, not records of client projects or measured results. Service descriptions should be discussed with TechCloud to confirm suitability and scope.',
        'Using the website' => 'Please use the website lawfully. Do not interfere with its operation, attempt unauthorised access or submit malicious material. Please contact TechCloud before reusing website branding or other content; third-party materials remain subject to their owners’ rights.',
        'Links to other websites' => 'Maps, the shop destination and other external links may open services operated by third parties. Their own terms and privacy practices apply. Check those terms before providing information or making a purchase.',
        'Questions and service enquiries' => 'Use the Contact page to discuss a requirement or ask about website content. The shared design preview does not send contact-form submissions or accept orders.',
    ]],
    'privacy-policy' => ['Privacy Policy', 'How information is handled in this website preview.', [
        'The shared preview' => 'This is a design preview. Its contact form validates entries in your browser and shows a demonstration response; it does not send or save an enquiry. Please do not enter sensitive or confidential information into a preview form.',
        'Contacting TechCloud' => 'Email and telephone links open your chosen application. If you contact TechCloud through those channels, you provide the information in your message directly to the business. Include only the details needed to discuss your enquiry.',
        'Website hosting and embedded maps' => 'The preview is hosted on GitHub Pages. The hosting provider and external services may process technical information such as your IP address and browser details when you visit. The footer contains an embedded Google Map, which connects to Google when it loads. Third-party privacy policies apply to those services.',
        'Before the full website launches' => 'The production privacy notice must confirm the business contact for privacy enquiries, information collected through the live form, purposes of use, service providers, retention arrangements and handling of requests. This draft does not establish those unconfirmed operational details.',
        'Privacy enquiries' => 'Please use the Contact page to ask TechCloud about personal information supplied directly to the business, including requests concerning access, correction or withdrawal of consent. Do not submit identity documents through this review preview.',
    ]],
    'cookies' => ['Cookies', 'Cookies, embedded services and browser controls.', [
        'About cookies' => 'Cookies are small pieces of information stored by a browser. Websites and embedded services may use them for functions such as remembering settings or maintaining a session.',
        'This design preview' => 'The preview code does not set first-party cookies or use local storage. No analytics or advertising tags have been added to this build. Its fonts, images and hero video are served with the website.',
        'Google Maps and external links' => 'The embedded Google Map loads content from Google and may use cookies or similar technologies under Google’s policies. Opening the map, email application, shop or designer link takes you to a separate service with its own practices. This page is an information notice, not a consent control that blocks the embedded map.',
        'Browser settings' => 'You can review or remove stored cookies and limit third-party cookies in your browser’s privacy settings. Restricting them may affect embedded features. Settings vary between browsers.',
        'Before launch' => 'The cookie information must be reviewed against the final hosting, WordPress plugins, analytics and third-party services. Any required preference controls should be implemented and checked against that final configuration.',
    ]],
];
foreach ($pages as $slug => [$title, $excerpt, $sections]) {
    $existing = get_page_by_path($slug);
    if ($existing && $existing->post_status === 'publish') continue;
    $content = '<!-- wp:group {"className":"policy-content"} --><div class="wp-block-group policy-content">';
    $content .= '<!-- wp:paragraph {"className":"policy-review-note"} --><p class="policy-review-note">Draft for review. TechCloud must confirm this policy before the full website launches.</p><!-- /wp:paragraph -->';
    foreach ($sections as $heading => $copy) {
        $content .= '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html($heading) . '</h2><!-- /wp:heading -->';
        $content .= '<!-- wp:paragraph --><p>' . esc_html($copy) . '</p><!-- /wp:paragraph -->';
    }
    $content .= '<!-- wp:paragraph --><p><a href="' . esc_url(home_url('/contact/')) . '">Contact TechCloud</a></p><!-- /wp:paragraph --></div><!-- /wp:group -->';
    $data = ['post_type'=>'page', 'post_status'=>'publish', 'post_title'=>$title, 'post_name'=>$slug, 'post_excerpt'=>$excerpt, 'post_content'=>$content];
    if ($existing) {
        $data['ID'] = $existing->ID;
        update_post_meta($existing->ID, '_tc_before_policy_draft', $existing->post_content);
    }
    $id = wp_insert_post($data, true);
    if (is_wp_error($id)) WP_CLI::error($id->get_error_message());
    update_post_meta($id, '_tc_policy_draft', 1);
}
update_option('tc_policy_pages_added', gmdate('c'));
WP_CLI::success('Three editable policy drafts are ready for review.');
