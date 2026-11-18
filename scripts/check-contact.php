<?php
// Local integration checks: mail is intercepted; every test record is removed.
if (!defined('WP_CLI') || !WP_CLI) exit;
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) WP_CLI::error('Local installations only.');
$records = []; $keys = []; $mail = []; $mail_result = true;
$original_bcc = get_option('tc_enquiry_bcc', null);
$intercept = function ($pre, $args) use (&$mail, &$mail_result) { $mail[] = $args; return $mail_result; };
add_filter('pre_wp_mail', $intercept, 10, 2);
$assert = function ($condition, $message) { if (!$condition) throw new RuntimeException($message); };
$payload = ['tc_nonce' => wp_create_nonce('tc_send_enquiry'), 'submission_token' => wp_generate_uuid4(), 'name' => "Zoë O'Neil", 'email' => 'qa@example.test', 'company' => 'Local test only', 'phone' => '+65 6123 4567', 'service' => 'infrastructure', 'message' => "Test only: office setup at C:\\Office. Do not send email.", 'website' => ''];
$key_for = function ($ip, $token) use (&$keys) {
    $identity = hash_hmac('sha256', $ip, wp_salt('nonce'));
    $keys[] = 'tc_enquiry_rate_' . $identity;
    $keys[] = 'tc_enquiry_done_' . hash('sha256', $identity . $token);
};
try {
    update_option('tc_enquiry_bcc', 'audit@example.test');
    $assert(tc_enquiry_sanitize_bcc("audit@example.test\r\nBcc: other@example.test") === 'audit@example.test', 'BCC settings must reject header injection and retain the saved address.');
    $ip = '198.51.100.213'; $key_for($ip, $payload['submission_token']);
    foreach ($keys as $key) delete_transient($key);
    $result = tc_process_enquiry(array_merge($payload, ['tc_nonce' => 'invalid']), $ip);
    $assert(is_wp_error($result) && $result->get_error_code() === 'expired', 'Invalid nonce must reject.');
    $result = tc_process_enquiry(array_merge($payload, ['email' => "bad\r\nBcc: other@example.test", 'message' => '']), $ip);
    $assert(is_wp_error($result) && isset($result->get_error_data()['email'], $result->get_error_data()['message']), 'Field validation must reject and identify invalid fields.');
    $result = tc_process_enquiry(array_merge($payload, ['website' => 'spam']), $ip);
    $assert(is_wp_error($result) && $result->get_error_code() === 'spam', 'Honeypot must reject.');
    $assert(count($mail) === 0, 'Invalid submissions must not notify.');
    $id = tc_process_enquiry($payload, $ip);
    $assert(is_int($id), 'Valid submission must save.'); $records[] = $id;
    $assert(get_post_status($id) === 'private', 'Enquiries must be private.');
    $assert(get_post_field('post_content', $id) === $payload['message'], 'Message punctuation and backslashes must survive.');
    $assert(get_post_meta($id, '_tc_email', true) === 'qa@example.test', 'Reply details must be retained.');
    $assert(count($mail) === 1 && $mail[0]['to'] === tc_enquiry_email(), 'One notification must target the configured business email.');
    $assert($mail[0]['headers'] === ['Reply-To: qa@example.test', 'Bcc: audit@example.test'], 'Enquiry mail must retain Reply-To and include the configured BCC exactly once.');
    $assert(get_post_meta($id, '_tc_notification_bcc', true) === 'audit@example.test', 'The private enquiry must record its notification BCC.');
    $assert(tc_process_enquiry($payload, $ip) === $id && count($mail) === 1, 'Duplicate request must not duplicate storage or notification.');
    $next = array_merge($payload, ['submission_token' => wp_generate_uuid4()]);
    $result = tc_process_enquiry($next, $ip);
    $assert(is_wp_error($result) && $result->get_error_code() === 'rate', 'Rapid repeat submissions must be limited.');
    $mail_result = false; $ip = '198.51.100.214'; $key_for($ip, $next['submission_token']);
    update_option('tc_enquiry_bcc', '');
    delete_transient('tc_enquiry_rate_' . hash_hmac('sha256', $ip, wp_salt('nonce')));
    $id = tc_process_enquiry($next, $ip);
    $assert(is_int($id), 'Mail transport failure must preserve the enquiry.'); $records[] = $id;
    $assert($mail[1]['headers'] === ['Reply-To: qa@example.test'], 'An empty BCC setting must send no hidden copy.');
    $assert(str_contains(get_post_meta($id, '_tc_notification', true), 'failed'), 'Administrator must see notification failure.');
    $response = wp_remote_get('http://wordpress/?post_type=tc_enquiry&p=' . $id, ['headers' => ['Host' => 'localhost:8129'], 'redirection' => 0]);
    $assert(!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 404 && !str_contains(wp_remote_retrieve_body($response), $payload['message']), 'Anonymous visitors must not see private enquiry content.');
    $type = get_post_type_object('tc_enquiry');
    $assert(!$type->public && !$type->show_in_rest && $type->cap->read_post === 'manage_options', 'Enquiries require administrator access and no REST exposure.');
    WP_CLI::success('Contact validation, nonce, spam controls, duplicate handling, private storage and notification-failure retention pass. No mail sent.');
} finally {
    if ($original_bcc === null) delete_option('tc_enquiry_bcc'); else update_option('tc_enquiry_bcc', $original_bcc);
    remove_filter('pre_wp_mail', $intercept, 10);
    foreach ($records as $id) wp_delete_post($id, true);
    foreach (array_unique($keys) as $key) delete_transient($key);
}
