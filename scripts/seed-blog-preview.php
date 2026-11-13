<?php
/** Local-only design fixtures. Never include these posts in a production import. */
if (!defined('WP_CLI') || !WP_CLI) exit;
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) WP_CLI::error('Blog samples are local-only.');
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
$samples = [
    ['Disaster Recovery Planning: 5-Step Emergency Plan for SMEs', 'disaster-recovery-planning-5-step-emergency-plan-for-smes', 'Business continuity', 'infrastructure.webp', 'A clear plan helps your team respond when everyday systems are interrupted. Start with the people, priorities and information you depend on.'],
    ['What to look for in a managed IT support partner', 'choosing-a-managed-it-support-partner', 'IT support', 'support-poster.webp', 'The right support starts with an understanding of your team. These questions can help you shape a useful first conversation.'],
    ['Planning an office move? Start with your IT.', 'planning-it-for-an-office-move', 'Infrastructure', 'workplace.webp', 'From connectivity to workstations, a little preparation helps you bring people and technology together in a new space.'],
    ['Backups are only the beginning: planning a recovery test', 'planning-a-backup-recovery-test', 'Business continuity', 'managed-it-poster.webp', 'A backup is more useful when your team knows how to restore it. Make time to check the process before you need it.'],
    ['A practical checklist for your next device rollout', 'device-rollout-checklist', 'IT support', 'support-poster.webp', 'Think beyond the equipment order. Plan access, configuration, handover and support around the people using each device.'],
    ['Building a network that can grow with your team', 'building-a-network-for-a-growing-team', 'Infrastructure', 'network-poster.webp', 'An expanding team brings new demands on connectivity. Review your current setup before deciding what to change.'],
    ['Keeping your recovery plan up to date', 'keeping-your-recovery-plan-up-to-date', 'Business continuity', 'infrastructure.webp', 'People, suppliers and systems change. A regular review helps keep the details in your recovery plan useful.'],
];
foreach ($samples as $i => [$title, $slug, $category, $image, $excerpt]) {
    $existing = get_page_by_path($slug, OBJECT, 'post');
    if ($existing) { WP_CLI::log('Kept existing post: ' . $slug); continue; }
    $term = term_exists($category, 'category') ?: wp_insert_term($category, 'category');
    if (is_wp_error($term)) WP_CLI::error($term->get_error_message());
    $content = '<!-- wp:paragraph --><p><em>Sample article for design review. Final TechCloud blog content will be imported separately.</em></p><!-- /wp:paragraph -->';
    $paragraphs = $i === 0 ? [
        ['Start with the systems that matter', 'List the services your business needs to operate, and identify who uses them. Include the dependencies that are easy to overlook, such as network access, identity systems and third-party contacts.'],
        ['1. Agree on your priorities', 'Talk to the people responsible for each business function. Decide which systems need attention first, and record the order in which they should be restored.'],
        ['2. Know where your information lives', 'Keep an inventory of important data and the systems that hold it. Record how backups are managed and who can help with a restore.'],
        ['3. Make responsibilities clear', 'Name the people who will coordinate the response, contact suppliers and keep colleagues informed. Keep an accessible copy of the contact list.'],
        ['4. Walk through the recovery process', 'Choose a manageable scenario and test the documented steps in an agreed environment. Record any missing information and practical issues the team encounters.'],
        ['5. Review after every change', 'Update the plan when people, systems or suppliers change. Use the findings from each exercise to improve the next version.'],
    ] : [
        ['Start with your business', 'Before choosing a solution, make a clear picture of how your team works today. Involve the people who use the systems every day and record the issues they want to resolve.'],
        ['Make the scope clear', 'Write down what needs to happen, who is responsible and which dependencies could affect the work. Agree on a practical sequence and a way to track progress.'],
        ['Plan the handover', 'Keep the relevant information together and make sure your team knows where to find help. Review the result with the people who will use it and record any follow-up work.'],
    ];
    $content .= '<!-- wp:paragraph --><p>' . esc_html($excerpt) . '</p><!-- /wp:paragraph -->';
    foreach ($paragraphs as [$heading, $paragraph]) {
        $content .= '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html($heading) . '</h2><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html($paragraph) . '</p><!-- /wp:paragraph -->';
    }
    $content .= '<!-- wp:quote --><blockquote class="wp-block-quote"><!-- wp:paragraph --><p>A useful plan is one your team can find, understand and put into practice.</p><!-- /wp:paragraph --></blockquote><!-- /wp:quote -->';
    $id = wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug, 'post_excerpt' => $excerpt, 'post_content' => $content, 'post_date' => sprintf('2026-10-%02d 09:00:00', 28 - $i * 3), 'post_category' => [(int) $term['term_id']], 'comment_status' => 'closed'], true);
    if (is_wp_error($id)) WP_CLI::error($id->get_error_message());
    update_post_meta($id, '_tc_blog_design_sample', 1);
    $asset = get_theme_file_path('assets/' . $image);
    $temp = wp_tempnam($image);
    copy($asset, $temp);
    $attachment = media_handle_sideload(['name' => $image, 'tmp_name' => $temp], $id, 'TechCloud blog design preview');
    if (is_wp_error($attachment)) WP_CLI::error($attachment->get_error_message());
    set_post_thumbnail($id, $attachment);
    WP_CLI::log('Created sample: ' . $slug);
}
// Keep WordPress's untouched installation example out of the review listing.
$hello = get_page_by_path('hello-world', OBJECT, 'post');
if ($hello && $hello->post_title === 'Hello world!' && $hello->post_status === 'publish') wp_update_post(['ID' => $hello->ID, 'post_status' => 'draft']);
WP_CLI::success('Local blog fixtures ready. These samples are not approved publication content.');
