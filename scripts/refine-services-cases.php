<?php
// Local review content only. Preserve existing copy and never overwrite later edits.
if (!defined('WP_CLI') || !WP_CLI) exit;
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) WP_CLI::error('Local installations only.');
if (get_option('tc_services_cases_expanded')) { WP_CLI::success('Expanded content already installed.'); return; }
function tc_rich_p($text) { return '<!-- wp:paragraph --><p>' . esc_html($text) . '</p><!-- /wp:paragraph -->'; }
function tc_rich_h($text, $level = 2) { return '<!-- wp:heading {"level":' . $level . '} --><h' . $level . ' class="wp-block-heading">' . esc_html($text) . '</h' . $level . '><!-- /wp:heading -->'; }
function tc_rich_group($class, $content) { return '<!-- wp:group ' . wp_json_encode(['className' => $class]) . ' --><div class="wp-block-group ' . esc_attr($class) . '">' . $content . '</div><!-- /wp:group -->'; }
function tc_rich_link($label, $path) { return '<!-- wp:paragraph {"className":"service-detail-link"} --><p class="service-detail-link"><a href="' . esc_url(home_url($path)) . '">' . esc_html($label) . '</a></p><!-- /wp:paragraph -->'; }
function tc_rich_list($items) {
    $content = ''; foreach ($items as $item) $content .= '<!-- wp:list-item --><li>' . esc_html($item) . '</li><!-- /wp:list-item -->';
    return '<!-- wp:list --><ul class="wp-block-list">' . $content . '</ul><!-- /wp:list -->';
}
function tc_rich_faq($question, $answer) { return '<!-- wp:details --><details class="wp-block-details"><summary>' . esc_html($question) . '</summary>' . tc_rich_p($answer) . '</details><!-- /wp:details -->'; }
function tc_rich_update($post, $content, $extra = []) {
    if (!metadata_exists('post', $post->ID, '_tc_before_expanded_content')) update_post_meta($post->ID, '_tc_before_expanded_content', $post->post_content);
    $result = wp_update_post(array_merge(['ID' => $post->ID, 'post_content' => $content], $extra), true);
    if (is_wp_error($result)) WP_CLI::error($result->get_error_message());
}
$services = [
    'managed-it-services' => [
        'heading' => 'Keep your attention on the business.',
        'intro' => 'Servers, networks and workstations need attention between breakdowns. Managed IT brings their monitoring and maintenance into an agreed support plan, with technical help for the systems your team uses each day.',
        'fit' => 'Useful when IT tasks are spread across several people, maintenance is slipping, or an internal team needs support with day-to-day infrastructure.',
        'image' => 'managed-it-poster.webp',
        'scope' => [
            'Infrastructure monitoring' => 'Review the systems in scope and the issues that need attention. Monitoring arrangements depend on your equipment, access and operating requirements.',
            'Maintenance and technical support' => 'Plan routine checks and maintenance around your working hours. Agree how faults are reported and which systems the support arrangement covers.',
            'Environment reviews' => 'Look at the current setup, recurring problems and equipment needs. Use the review to prioritise maintenance and future changes.',
            'Support planning' => 'Define responsibilities, escalation contacts and the level of assistance needed. Coverage and response arrangements belong in the agreed service scope.',
        ],
        'prepare' => ['The number of users, locations and devices', 'Your current servers, network and business applications', 'Recurring issues and existing support arrangements', 'Working hours and systems that are critical to daily operations'],
        'faq' => [
            'Can this work alongside our internal IT team?' => 'Yes. Describe what your team manages today and where it needs assistance. Responsibilities can then be divided within the agreed scope.',
            'What coverage and response times are included?' => 'Coverage, response arrangements and any on-site requirements are agreed for your environment. Ask for these details in the proposal before choosing a support arrangement.',
        ],
        'related' => ['end-user-it-support' => 'End-user IT support', 'it-installation-infrastructure' => 'IT installation & infrastructure'],
    ],
    'end-user-it-support' => [
        'heading' => 'Help your team get back to work.',
        'intro' => 'A laptop issue, a software error or an unreliable connection can interrupt someone’s whole morning. Give employees a route to on-site or remote technical help for the devices and applications they use at work.',
        'fit' => 'Useful for businesses that need help with everyday IT issues, recurring workstation problems or additional support for employees across locations.',
        'image' => 'support-poster.webp',
        'scope' => [
            'On-site and remote troubleshooting' => 'Describe the problem and the affected device. The support route can be agreed around the issue, access requirements and whether hands-on work is needed.',
            'Hardware and workstation assistance' => 'Help with desktop and laptop issues, connected equipment and device setup. Identify the equipment involved so the team can assess the next step.',
            'Software installation and configuration' => 'Discuss software setup, updates and configuration for your working environment. Licensing and application access need to be confirmed before work begins.',
            'Connectivity and user guidance' => 'Investigate connection issues and explain the steps your team needs to take. Practical guidance helps staff use their equipment with more confidence.',
        ],
        'prepare' => ['Which users and devices are affected', 'The error message and when it started', 'Any recent software or equipment changes', 'Your location and whether remote access is available'],
        'faq' => [
            'Can someone visit our office?' => 'On-site and remote support are available options. The location, issue, timing and scope need to be agreed with the team.',
            'Can we arrange support for recurring issues?' => 'Yes. Share the problems that keep returning and the number of users affected. This helps the team discuss an appropriate support arrangement.',
        ],
        'related' => ['managed-it-services' => 'Managed IT services', 'it-staffing' => 'IT staffing'],
    ],
    'it-staffing' => [
        'heading' => 'Add expertise where it matters.',
        'intro' => 'A project may need more hands than your existing team can provide. IT staffing helps you discuss the technical skills and capacity needed for temporary assignments or longer-term roles, on-site or remotely.',
        'fit' => 'Useful for a defined project, additional operational capacity or a longer-term skills requirement. Start with the work the person will actually be responsible for.',
        'image' => 'workplace.webp',
        'scope' => [
            'Temporary and longer-term requirements' => 'Set out the expected assignment length, workload and responsibilities. Discuss the type of arrangement that fits the role and your business.',
            'Technical skills matching' => 'Specify the systems, tools and experience the assignment needs. Clear requirements help distinguish essential skills from experience that would be useful.',
            'On-site and remote working' => 'Agree the working location, hours and reporting line. Access to your systems and internal onboarding should be planned before the assignment starts.',
            'Additional project capacity' => 'Identify the tasks your current team cannot absorb. Define the handover, supervision and expected deliverables for the additional resource.',
        ],
        'prepare' => ['Role responsibilities and required technical skills', 'Expected start date and assignment duration', 'Working location, hours and reporting line', 'Budget range and onboarding requirements'],
        'faq' => [
            'Can we request help for a short project?' => 'Yes. Share the project tasks, skills and dates so the team can discuss a temporary staffing requirement. Availability is confirmed during the discussion.',
            'How soon can an assignment start?' => 'Timing depends on the skills, availability and onboarding requirements. Provide your target date early so it can be considered in the proposed arrangement.',
        ],
        'related' => ['project-management' => 'Project management', 'end-user-it-support' => 'End-user IT support'],
    ],
    'project-management' => [
        'heading' => 'Give your IT project a workable plan.',
        'intro' => 'Office moves, equipment upgrades and system changes involve decisions across teams. Project management brings the scope, schedule and implementation activities together so everyone understands what needs to happen next.',
        'fit' => 'Useful when a technology change involves several suppliers, competing deadlines or work that must be coordinated around business operations.',
        'image' => 'infrastructure.webp',
        'scope' => [
            'Scope and planning' => 'Define the project requirements, boundaries and expected deliverables. Identify dependencies and the decisions needed before implementation can begin.',
            'Implementation coordination' => 'Coordinate agreed activities with the people involved. Consider access, equipment readiness and the sequence of work when planning the rollout.',
            'Progress and risk communication' => 'Keep stakeholders informed about progress, open decisions and issues. Discuss changes to scope or timing as they arise.',
            'Handover and review' => 'Review completed work against the agreed scope and identify outstanding items. Discuss any follow-up support needed after implementation.',
        ],
        'prepare' => ['The change you want to make and why', 'Target dates, budget and business constraints', 'The teams and suppliers involved', 'Known dependencies and how completion will be assessed'],
        'faq' => [
            'Can you coordinate an office IT move?' => 'An office move can be discussed as a defined project. Share the locations, target date, equipment and other suppliers involved so the scope can be assessed.',
            'What happens after implementation?' => 'The handover and any follow-up assistance are agreed as part of the project. Discuss documentation, outstanding items and ongoing support before work begins.',
        ],
        'related' => ['it-installation-infrastructure' => 'IT installation & infrastructure', 'it-staffing' => 'IT staffing'],
    ],
    'it-installation-infrastructure' => [
        'heading' => 'Build on the right foundations.',
        'intro' => 'An office needs its equipment, network and workstations to work together. Plan the installation around the space, the people using it and the systems already in place, from a new office setup to an infrastructure refresh.',
        'fit' => 'Useful when you are moving, adding workstations, replacing ageing equipment or preparing a new business location.',
        'image' => 'network-poster.webp',
        'scope' => [
            'Server and workstation installation' => 'Discuss equipment requirements and where each device will be used. Installation can then be planned around the agreed configuration and access needs.',
            'Network equipment setup' => 'Plan the networking equipment required for the space and users. Confirm existing connections, equipment compatibility and the areas that need connectivity.',
            'Equipment provisioning' => 'Prepare devices and their agreed software configuration. Confirm licences, permissions and the information needed before setup begins.',
            'Office upgrades and handover' => 'Coordinate changes with your office schedule. Discuss checks, user guidance and any onboarding needed after the installation.',
        ],
        'prepare' => ['Office layout, user count and planned growth', 'Existing equipment and items to be replaced', 'Network and application requirements', 'Access dates, installation windows and budget'],
        'faq' => [
            'Can existing equipment be reused?' => 'Share the models, condition and current configuration. Suitability can be reviewed against the requirements of the planned setup.',
            'How is an installation priced?' => 'The scope depends on the equipment, scale and complexity of the work. A discussion of your requirements comes before an itemised proposal.',
        ],
        'related' => ['project-management' => 'Project management', 'managed-it-services' => 'Managed IT services'],
    ],
];
foreach ($services as $slug => $data) {
    $page = get_page_by_path('services/' . $slug);
    if (!$page) WP_CLI::error('Missing service: ' . $slug);
    $image = '<!-- wp:image {"sizeSlug":"large","className":"service-detail-photo"} --><figure class="wp-block-image size-large service-detail-photo"><img src="' . esc_url(get_theme_file_uri('assets/' . $data['image'])) . '" alt="Illustrative ' . esc_attr(strtolower($page->post_title)) . ' environment" width="960" height="540" loading="lazy" decoding="async"/></figure><!-- /wp:image -->';
    $overview = tc_rich_group('service-detail-overview', tc_rich_group('service-detail-intro', tc_rich_h($data['heading']) . tc_rich_p($data['intro']) . tc_rich_p($data['fit'])) . $image);
    $rows = ''; foreach ($data['scope'] as $title => $text) $rows .= tc_rich_group('service-capability', tc_rich_h($title, 3) . tc_rich_p($text));
    $scope = tc_rich_group('service-capabilities', tc_rich_h('What we can help with') . tc_rich_group('service-capability-list', $rows));
    $prepare = tc_rich_group('service-preparation', tc_rich_h('A useful starting point') . tc_rich_p('Bring these details to the first conversation:') . tc_rich_list($data['prepare']) . tc_rich_link('Discuss your requirements', '/contact/#contact-form'));
    $faq = ''; foreach ($data['faq'] as $question => $answer) $faq .= tc_rich_faq($question, $answer);
    $questions = tc_rich_group('service-questions', tc_rich_h('Before we begin') . $faq);
    $related = ''; foreach ($data['related'] as $path => $label) $related .= tc_rich_link($label, '/services/' . $path . '/');
    $content = tc_rich_group('service-detail', $overview . $scope . tc_rich_group('service-planning', $prepare . $questions) . tc_rich_group('service-related', tc_rich_h('Related services', 3) . $related));
    tc_rich_update($page, $content);
}
// Add a practical overview below the existing service selector.
$overview = get_page_by_path('services');
$guide = tc_rich_h('Find the support for your next step.') . tc_rich_p('Choose a starting point below. Services can be discussed together when your requirements overlap.');
foreach ($services as $slug => $data) {
    $page = get_page_by_path('services/' . $slug);
    $guide .= tc_rich_group('service-guide-row', tc_rich_h($page->post_title, 3) . tc_rich_p($data['fit']) . tc_rich_link('View service', '/services/' . $slug . '/'));
}
$blocks = parse_blocks($overview->post_content); $content = '';
foreach ($blocks as $i => $block) {
    $content .= serialize_block($block);
    if ($i === 0) $content .= tc_rich_group('service-guide', $guide);
}
tc_rich_update($overview, $content);

$cases = [
    'connected-workplace-example' => [
        'title' => 'A more connected workplace.',
        'excerpt' => 'Planning the network, equipment and handover for an office move.',
        'situation' => 'A growing business is preparing to move office. Its existing equipment needs to be reviewed, new workstations need to be set up and the network needs to suit the new layout. The move date creates a fixed point for the planning.',
        'approach' => 'Start with the floor plan, user count and an inventory of equipment. Check what can be reused and what needs to be sourced. Coordinate access and installation dates with the office team and other suppliers before scheduling the changeover.',
        'scope' => ['Review equipment and the new office layout', 'Plan network equipment and workstation placement', 'Coordinate installation and device configuration', 'Check connections and record outstanding handover items'],
        'review' => 'For this scenario, the handover would check whether workstations connect as intended, required applications are available and someone owns each outstanding issue. Actual completion and outcomes would need to be recorded after a real project.',
        'related' => ['it-installation-infrastructure' => 'IT installation & infrastructure', 'project-management' => 'Project management'],
    ],
    'growing-team-support-example' => [
        'title' => 'Support for a growing team.',
        'excerpt' => 'Giving employees a clear route to help with devices, software and connections.',
        'situation' => 'A business has added employees across its office and remote team. Staff are asking different colleagues for IT help, and recurring laptop and software issues are taking time away from their work.',
        'approach' => 'Review the devices and applications in use, then agree which issues the support arrangement will cover. Establish how employees request help and when an issue needs an on-site visit. Recurring problems can inform the next maintenance discussion.',
        'scope' => ['Review users, devices and recurring issues', 'Agree remote and on-site support arrangements', 'Assist with workstation and software troubleshooting', 'Provide practical guidance for affected employees'],
        'review' => 'A real engagement could review the types of requests received and which problems continue to recur. Response expectations and coverage would be agreed before support starts. This example assumes no response-time or productivity results.',
        'related' => ['end-user-it-support' => 'End-user IT support', 'managed-it-services' => 'Managed IT services'],
    ],
    'network-refresh-example' => [
        'title' => 'A planned network refresh.',
        'excerpt' => 'Preparing an infrastructure upgrade around the working day.',
        'situation' => 'An office is reviewing ageing network equipment after repeated connection problems. The business needs to understand what should be replaced and how the change could be scheduled around employees who rely on the network.',
        'approach' => 'Document the existing equipment and the problems reported by users. Review compatibility and installation requirements before agreeing the replacement scope. Plan the work window, dependencies and checks with the people responsible for daily operations.',
        'scope' => ['Review current infrastructure and connection issues', 'Agree equipment requirements and installation scope', 'Coordinate the change window and responsible contacts', 'Check the updated setup and document follow-up needs'],
        'review' => 'The review would compare the completed configuration with the agreed plan and check connections in the affected areas. Any claim about fewer interruptions or improved performance would require measurements from the real environment.',
        'related' => ['it-installation-infrastructure' => 'IT installation & infrastructure', 'managed-it-services' => 'Managed IT services'],
    ],
    'device-rollout-example' => [
        'title' => 'Extra hands for a device rollout.',
        'excerpt' => 'Matching temporary IT capacity to a defined equipment project.',
        'situation' => 'A business plans to replace employee workstations while its internal IT team continues handling daily support. The rollout needs additional technical capacity for a defined period and a clear division of responsibilities.',
        'approach' => 'List the devices, locations and configuration tasks, then define the skills and dates needed. Agree who supervises the assignment and how completed work will be checked. Coordinate staff onboarding and system access before the rollout begins.',
        'scope' => ['Define the temporary role and required technical skills', 'Agree working location, assignment dates and reporting line', 'Coordinate device preparation and employee setup', 'Record completed tasks and unresolved support items'],
        'review' => 'The proposed handover would reconcile devices against the rollout list and pass remaining issues to the internal team. Staffing availability, the schedule and deliverables would need to be confirmed for the actual assignment.',
        'related' => ['it-staffing' => 'IT staffing', 'project-management' => 'Project management'],
    ],
];
$order = 0;
foreach ($cases as $slug => $data) {
    $content = tc_rich_h('The situation') . tc_rich_p($data['situation']) . tc_rich_h('A proposed approach') . tc_rich_p($data['approach']) . tc_rich_h('Work to consider') . tc_rich_list($data['scope']) . tc_rich_h('What the handover would check') . tc_rich_p($data['review']);
    $related = ''; foreach ($data['related'] as $path => $label) $related .= tc_rich_link($label, '/services/' . $path . '/');
    $content .= tc_rich_group('service-related', tc_rich_h('Relevant services', 3) . $related);
    $existing = get_page_by_path($slug, OBJECT, 'tc_case');
    $fields = ['post_type' => 'tc_case', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $data['title'], 'post_excerpt' => $data['excerpt'], 'post_content' => $content, 'menu_order' => ++$order];
    if ($existing) {
        if (!get_post_meta($existing->ID, '_tc_example', true)) WP_CLI::error('Refusing to overwrite a non-example case.');
        tc_rich_update($existing, $content, $fields); $id = $existing->ID;
    } else {
        $id = wp_insert_post($fields, true);
        if (is_wp_error($id)) WP_CLI::error($id->get_error_message());
    }
    update_post_meta($id, '_tc_example', 1);
}
update_option('tc_services_cases_expanded', gmdate('c'));
WP_CLI::success('Five detailed services and four illustrative cases installed. Previous content retained.');
