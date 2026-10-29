<?php
// One-time editorial refinement of the local review content. Keep CMS edits on reruns.
if (!defined('WP_CLI') || !WP_CLI) exit;
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) WP_CLI::error('Local installations only.');
if (get_option('tc_refined_v3')) { WP_CLI::success('V3 content already installed.'); return; }

function tc_v3_p($text) {
    return '<!-- wp:paragraph --><p>' . esc_html($text) . '</p><!-- /wp:paragraph -->';
}
function tc_v3_h($text, $level = 2) {
    return '<!-- wp:heading {"level":' . $level . '} --><h' . $level . ' class="wp-block-heading">' . esc_html($text) . '</h' . $level . '><!-- /wp:heading -->';
}
function tc_v3_group($class, $content) {
    return '<!-- wp:group ' . wp_json_encode(['className' => $class]) . ' --><div class="wp-block-group ' . esc_attr($class) . '">' . $content . '</div><!-- /wp:group -->';
}
function tc_v3_shortcode($text) {
    return '<!-- wp:shortcode -->' . $text . '<!-- /wp:shortcode -->';
}
function tc_v3_details($question, $answer) {
    return '<!-- wp:details --><details class="wp-block-details"><summary>' . esc_html($question) . '</summary>' . tc_v3_p($answer) . '</details><!-- /wp:details -->';
}
function tc_v3_update($post, $values) {
    if (!$post) WP_CLI::error('An expected review page is missing.');
    if (!metadata_exists('post', $post->ID, '_tc_before_v3')) {
        update_post_meta($post->ID, '_tc_before_v3', array_intersect_key((array) $post, array_flip(['post_title', 'post_excerpt', 'post_content'])));
    }
    $result = wp_update_post(wp_slash(array_merge(['ID' => $post->ID], $values)), true);
    if (is_wp_error($result)) WP_CLI::error($result->get_error_message());
}

$cta = tc_v3_shortcode('[techcloud_cta]');
$services = tc_v3_group('section home-services', tc_v3_group('section-head', tc_v3_h('Our services')) . tc_v3_shortcode('[techcloud_services]'));
$cases = tc_v3_group('section home-case', tc_v3_group('section-head', tc_v3_h('Case studies')) . tc_v3_shortcode('[techcloud_cases limit="1"]'));
$faq = tc_v3_group('faq-section', tc_v3_group('faq-intro', tc_v3_h('Before you get in touch')) . tc_v3_group('faq-items',
    tc_v3_details('Can you provide on-site support?', 'Yes. End-user support can include both on-site and remote assistance. The arrangement depends on your location, equipment and agreed scope.') .
    tc_v3_details('Can we engage you for one project?', 'Yes. You can discuss a specific installation, office move, infrastructure upgrade or staffing requirement with us.') .
    tc_v3_details('What information should we send?', 'Start with your business location, the number of people or devices involved, and a brief description of the issue or planned work. Include your preferred timing if you have one.')
));
tc_v3_update(get_post((int) get_option('page_on_front')), [
    'post_title' => 'IT support for Singapore businesses.',
    'post_excerpt' => 'Day-to-day technical support, IT staffing and infrastructure setup. Tell us what your team needs help with.',
    'post_content' => $services . $cases . $faq . $cta,
]);
tc_v3_update(get_page_by_path('services'), [
    'post_title' => 'IT services',
    'post_excerpt' => 'Support for your users, systems and projects. Choose a service to see what is included.',
    'post_content' => tc_v3_group('section', tc_v3_shortcode('[techcloud_services]')) . $cta,
]);

$service_copy = [
    'managed-it-services' => ['Monitoring, maintenance and technical support for your IT environment.', 'Keep your attention on the business.', 'Managing your IT environment', 'Bring monitoring, maintenance, and infrastructure management together with support suited to your environment.', 'Monitoring, routine maintenance and technical support for the infrastructure your business uses each day.'],
    'end-user-it-support' => ['On-site and remote help with devices, software and connectivity.', 'Help your team get back to work.', 'Support for your users', 'From connectivity issues to hardware and software questions, give your people a clear route to technical help.', 'Help with workstation faults, software issues and connectivity, delivered on-site or remotely according to the agreed scope.'],
    'it-staffing' => ['IT professionals for temporary assignments and longer-term roles.', 'Add expertise where it matters.', 'Temporary and longer-term staffing', 'Support your team with IT professionals for short-term projects or longer-term needs, with the scope shaped around your requirements.', 'Discuss the skills, working arrangements and duration you need, whether for a project or an ongoing role.'],
    'project-management' => ['Planning, implementation coordination and handover for IT projects.', 'Move from a good idea to a clear outcome.', 'Planning and coordinating IT projects', 'Bring structure to your technology project, with coordinated planning, implementation, and communication from start to finish.', 'Define the scope, coordinate implementation and track progress through to handover.'],
    'it-installation-infrastructure' => ['Server, workstation and network setup for new or existing offices.', 'Build on the right foundations.', 'Office equipment and network setup', 'Whether you are moving office, expanding, or refreshing equipment, plan an IT setup that supports your team and how they work.', 'Installation and provisioning for office moves, expansions and equipment upgrades.'],
];
foreach ($service_copy as $slug => [$excerpt, $old_heading, $heading, $old_description, $description]) {
    $page = get_page_by_path('services/' . $slug);
    if (!$page) WP_CLI::error('Missing service: ' . $slug);
    tc_v3_update($page, ['post_excerpt' => $excerpt, 'post_content' => strtr($page->post_content, [esc_html($old_heading) => esc_html($heading), esc_html($old_description) => esc_html($description)])]);
}
$contact = get_page_by_path('contact');
tc_v3_update($contact, [
    'post_title' => 'Contact TechCloud',
    'post_excerpt' => 'Email or call us to discuss IT support, staffing or an upcoming project.',
    'post_content' => tc_v3_group('content-split', tc_v3_shortcode('[techcloud_contact]') . tc_v3_group('contact-note', tc_v3_h('Tell us what you need') . tc_v3_p('Include your business location, the equipment or users involved, and a brief description of the issue or planned work.') . tc_v3_p('If you have a target date for an installation or project, include that too.'))),
]);
$example = get_page_by_path('connected-workplace-example', OBJECT, 'tc_case');
if ($example && get_post_meta($example->ID, '_tc_example', true)) {
    tc_v3_update($example, ['post_title' => 'Planning IT for an office move', 'post_excerpt' => 'An example of planning the network, workstations and equipment for a new office.']);
}
update_option('tc_refined_v3', gmdate('c'));
WP_CLI::success('Editorial review content installed. Previous titles, excerpts and blocks retained in _tc_before_v3.');
