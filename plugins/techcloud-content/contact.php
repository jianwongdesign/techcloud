<?php
defined('ABSPATH') || exit;

add_action('init', function () {
    register_post_type('tc_enquiry', [
        'labels' => ['name' => 'Enquiries', 'singular_name' => 'Enquiry'],
        'public' => false, 'publicly_queryable' => false, 'exclude_from_search' => true,
        'show_ui' => true, 'show_in_rest' => false, 'rewrite' => false, 'query_var' => false,
        'menu_icon' => 'dashicons-email-alt', 'supports' => ['title', 'editor'],
        'capabilities' => array_fill_keys(['edit_post', 'read_post', 'delete_post', 'edit_posts', 'edit_others_posts', 'publish_posts', 'read_private_posts', 'delete_posts', 'delete_private_posts', 'delete_published_posts', 'delete_others_posts', 'edit_private_posts', 'edit_published_posts'], 'manage_options') + ['create_posts' => 'do_not_allow'],
        'map_meta_cap' => false,
    ]);
});

function tc_enquiry_email() {
    return sanitize_email(get_theme_mod('tc_email', 'hello@techcloud.sg'));
}
function tc_enquiry_services() {
    return ['managed-it' => 'Managed IT services', 'end-user-support' => 'End-user IT support', 'staffing' => 'IT staffing', 'project-management' => 'Project management', 'infrastructure' => 'IT installation & infrastructure', 'not-sure' => 'Not sure yet'];
}
function tc_enquiry_values($raw) {
    $values = [];
    foreach (['name', 'email', 'company', 'phone', 'service', 'message', 'website', 'submission_token'] as $field) {
        $value = isset($raw[$field]) && is_string($raw[$field]) ? trim($raw[$field]) : '';
        $values[$field] = $field === 'message' ? sanitize_textarea_field($value) : sanitize_text_field($value);
    }
    return $values;
}
function tc_enquiry_validate($values) {
    $errors = [];
    if (mb_strlen($values['name']) < 2 || mb_strlen($values['name']) > 120) $errors['name'] = 'Enter your name (2–120 characters).';
    if (!is_email($values['email']) || strlen($values['email']) > 254) $errors['email'] = 'Enter a valid email address.';
    if (mb_strlen($values['company']) > 160) $errors['company'] = 'Keep the company name under 160 characters.';
    if ($values['phone'] && !preg_match('/^[+0-9() .-]{6,40}$/', $values['phone'])) $errors['phone'] = 'Enter a phone number, or leave this field blank.';
    if (!array_key_exists($values['service'], tc_enquiry_services())) $errors['service'] = 'Choose a service, or select “Not sure yet”.';
    if (mb_strlen($values['message']) < 10 || mb_strlen($values['message']) > 5000) $errors['message'] = 'Describe what you need in 10–5,000 characters.';
    return $errors;
}

// Return the stored enquiry ID. A mail failure does not discard the enquiry.
function tc_process_enquiry($raw, $ip) {
    $nonce = isset($raw['tc_nonce']) && is_string($raw['tc_nonce']) ? $raw['tc_nonce'] : '';
    if (!wp_verify_nonce($nonce, 'tc_send_enquiry')) return new WP_Error('expired', 'This form has expired. Please review your details and try again.');
    $values = tc_enquiry_values($raw);
    if ($values['website']) return new WP_Error('spam', 'Your enquiry could not be submitted. Please email us directly.');
    $errors = tc_enquiry_validate($values);
    if ($errors) return new WP_Error('validation', 'Please check the highlighted fields.', $errors);
    if (!preg_match('/^[a-f0-9-]{36}$/', $values['submission_token'])) return new WP_Error('expired', 'Please reload the form and try again.');
    $identity = hash_hmac('sha256', (string) $ip, wp_salt('nonce'));
    $receipt_key = 'tc_enquiry_done_' . hash('sha256', $identity . $values['submission_token']);
    $existing = get_transient($receipt_key);
    if ($existing) return (int) $existing;
    $rate_key = 'tc_enquiry_rate_' . $identity;
    $rate = get_transient($rate_key);
    if ($rate && ($rate['count'] >= 5 || time() - $rate['last'] < 30)) return new WP_Error('rate', 'Please wait a moment before sending another enquiry, or email us directly.');
    $id = wp_insert_post(wp_slash([
        'post_type' => 'tc_enquiry', 'post_status' => 'private',
        'post_title' => $values['name'] . ($values['company'] ? ' — ' . $values['company'] : ''),
        'post_content' => $values['message'],
    ]), true);
    if (is_wp_error($id)) return new WP_Error('storage', 'We could not save your enquiry. Please try again or email us directly.');
    foreach (['name', 'email', 'company', 'phone', 'service'] as $key) update_post_meta($id, '_tc_' . $key, $values[$key]);
    set_transient($receipt_key, $id, HOUR_IN_SECONDS);
    set_transient($rate_key, ['count' => ($rate['count'] ?? 0) + 1, 'last' => time()], HOUR_IN_SECONDS);
    $service = tc_enquiry_services()[$values['service']];
    $message = "New website enquiry\n\nName: {$values['name']}\nEmail: {$values['email']}\nCompany: {$values['company']}\nPhone: {$values['phone']}\nService: {$service}\n\n{$values['message']}\n\nView in WordPress: " . admin_url('post.php?post=' . $id . '&action=edit');
    $sent = wp_mail(tc_enquiry_email(), 'TechCloud website enquiry #' . $id, $message, ['Reply-To: ' . sanitize_email($values['email'])]);
    update_post_meta($id, '_tc_notification', $sent ? 'Accepted by mail transport' : 'Email notification failed — enquiry retained here');
    return $id;
}

function tc_handle_enquiry() {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { wp_safe_redirect(home_url('/contact/#contact-form')); exit; }
    $raw = wp_unslash($_POST);
    $result = tc_process_enquiry($raw, $_SERVER['REMOTE_ADDR'] ?? 'unknown');
    if (is_wp_error($result)) {
        $feedback = wp_generate_password(32, false, false);
        set_transient('tc_form_' . $feedback, ['message' => $result->get_error_message(), 'errors' => $result->get_error_data() ?: [], 'values' => tc_enquiry_values($raw)], 10 * MINUTE_IN_SECONDS);
        wp_safe_redirect(add_query_arg('form_feedback', $feedback, home_url('/contact/')) . '#contact-form');
    } else {
        wp_safe_redirect(add_query_arg('enquiry', 'received', home_url('/contact/')) . '#contact-form');
    }
    exit;
}
add_action('admin_post_nopriv_tc_send_enquiry', 'tc_handle_enquiry');
add_action('admin_post_tc_send_enquiry', 'tc_handle_enquiry');

add_shortcode('techcloud_contact_form', function () {
    $feedback = []; $values = tc_enquiry_values([]); $errors = [];
    $key = isset($_GET['form_feedback']) && is_string($_GET['form_feedback']) ? sanitize_text_field(wp_unslash($_GET['form_feedback'])) : '';
    if (preg_match('/^[A-Za-z0-9]{32}$/', $key)) {
        $feedback = get_transient('tc_form_' . $key) ?: [];
        delete_transient('tc_form_' . $key);
        $values = $feedback['values'] ?? $values; $errors = $feedback['errors'] ?? [];
    }
    $received = isset($_GET['enquiry']) && $_GET['enquiry'] === 'received';
    $status = $feedback['message'] ?? ($received ? 'Thank you. Your enquiry has been received.' : '');
    $error_attrs = function ($name) use ($errors) { return isset($errors[$name]) ? ' aria-invalid="true" aria-describedby="tc-' . esc_attr($name) . '-error"' : ''; };
    $error_text = function ($name) use ($errors) { if (isset($errors[$name])) echo '<p class="field-error" id="tc-' . esc_attr($name) . '-error">' . esc_html($errors[$name]) . '</p>'; };
    ob_start(); ?>
    <form id="contact-form" class="enquiry-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-contact-form data-contact-email="<?php echo esc_attr(tc_enquiry_email()); ?>">
        <h2>Send us an enquiry</h2>
        <p class="form-intro">Tell us what you need help with. Fields marked * are required.</p>
        <div class="form-status <?php echo $feedback ? 'is-error' : 'is-success'; ?>" role="status" aria-live="polite" tabindex="-1" <?php echo $status ? '' : 'hidden'; ?>><p><?php echo esc_html($status); ?></p></div>
        <?php wp_nonce_field('tc_send_enquiry', 'tc_nonce', false); ?>
        <input type="hidden" name="action" value="tc_send_enquiry">
        <input type="hidden" name="submission_token" value="<?php echo esc_attr(wp_generate_uuid4()); ?>">
        <div class="form-trap" aria-hidden="true"><label for="tc-website">Leave this field empty</label><input id="tc-website" type="text" name="website" tabindex="-1" autocomplete="off"></div>
        <div class="form-fields">
            <div class="form-field"><label for="tc-name">Name *</label><input id="tc-name" name="name" autocomplete="name" maxlength="120" minlength="2" required value="<?php echo esc_attr($values['name']); ?>"<?php echo $error_attrs('name'); ?>><?php $error_text('name'); ?></div>
            <div class="form-field"><label for="tc-email">Email *</label><input id="tc-email" type="email" name="email" autocomplete="email" maxlength="254" required value="<?php echo esc_attr($values['email']); ?>"<?php echo $error_attrs('email'); ?>><?php $error_text('email'); ?></div>
            <div class="form-field"><label for="tc-company">Company <span>(optional)</span></label><input id="tc-company" name="company" autocomplete="organization" maxlength="160" value="<?php echo esc_attr($values['company']); ?>"<?php echo $error_attrs('company'); ?>><?php $error_text('company'); ?></div>
            <div class="form-field"><label for="tc-phone">Phone <span>(optional)</span></label><input id="tc-phone" type="tel" name="phone" autocomplete="tel" maxlength="40" value="<?php echo esc_attr($values['phone']); ?>"<?php echo $error_attrs('phone'); ?>><?php $error_text('phone'); ?></div>
            <div class="form-field form-field-wide"><label for="tc-service">What can we help with? *</label><select id="tc-service" name="service" required<?php echo $error_attrs('service'); ?>><option value="">Select a service</option><?php foreach (tc_enquiry_services() as $key => $label) : ?><option value="<?php echo esc_attr($key); ?>" <?php selected($values['service'], $key); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select><?php $error_text('service'); ?></div>
            <div class="form-field form-field-wide"><label for="tc-message">Your message *</label><textarea id="tc-message" name="message" rows="5" minlength="10" maxlength="5000" required<?php echo $error_attrs('message'); ?>><?php echo esc_textarea($values['message']); ?></textarea><?php $error_text('message'); ?></div>
        </div>
        <p class="form-note">We’ll use these details to respond to your enquiry.</p>
        <button class="button" type="submit">Send enquiry <svg class="arrow-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14"/></svg></button>
    </form>
    <?php return ob_get_clean();
});

add_action('add_meta_boxes_tc_enquiry', function () {
    add_meta_box('tc-enquiry-details', 'Contact details', function ($post) {
        foreach (['name' => 'Name', 'email' => 'Email', 'company' => 'Company', 'phone' => 'Phone', 'service' => 'Service', 'notification' => 'Notification'] as $key => $label) {
            $value = get_post_meta($post->ID, '_tc_' . $key, true);
            if ($key === 'service') $value = tc_enquiry_services()[$value] ?? $value;
            echo '<p><strong>' . esc_html($label) . ':</strong> ' . esc_html($value ?: '—') . '</p>';
        }
    }, 'tc_enquiry', 'side');
});
