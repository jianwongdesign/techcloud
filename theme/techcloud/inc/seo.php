<?php
defined('ABSPATH') || exit;
add_filter('pre_get_document_title', function($title) {
    return is_singular('page') ? (get_post_meta(get_queried_object_id(),'_tc_seo_title',true) ?: $title) : $title;
});
add_action('wp_head', function() {
    if(!is_singular('page')) return;
    $description=get_post_meta(get_queried_object_id(),'_tc_seo_description',true);
    if($description) echo '<meta name="description" content="'.esc_attr($description).'">' . "\n";
});
add_action('add_meta_boxes_page', function() {
    add_meta_box('tc-page-search','TechCloud search and service card text',function($post) {
        wp_nonce_field('tc_save_search','tc_search_nonce');
        foreach(['_tc_seo_title'=>'Search title','_tc_seo_description'=>'Search description','_tc_service_label'=>'Short service name','_tc_service_summary'=>'Service card summary'] as $key=>$label) {
            echo '<p><label for="'.esc_attr($key).'"><strong>'.esc_html($label).'</strong></label><br><textarea style="width:100%" rows="2" id="'.esc_attr($key).'" name="'.esc_attr($key).'">'.esc_textarea(get_post_meta($post->ID,$key,true)).'</textarea></p>';
        }
    });
});
add_action('save_post_page',function($id) {
    if(wp_is_post_revision($id) || wp_is_post_autosave($id) || !current_user_can('edit_post',$id)) return;
    if(!isset($_POST['tc_search_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tc_search_nonce'])),'tc_save_search')) return;
    foreach(['_tc_seo_title','_tc_seo_description','_tc_service_label','_tc_service_summary'] as $key) {
        if(isset($_POST[$key]) && is_string($_POST[$key])) update_post_meta($id,$key,sanitize_textarea_field(wp_unslash($_POST[$key])));
    }
});
