<?php
// One-time local content migration from the supplied client brief. Preserve rollback data.
if (!defined('WP_CLI') || !WP_CLI) exit;
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) WP_CLI::error('Local installations only.');
if (get_option('tc_client_brief_20250303')) { WP_CLI::success('Client brief already applied.'); return; }
$data = json_decode(file_get_contents(__DIR__ . '/client-brief-content.json'), true, 512, JSON_THROW_ON_ERROR);
function tc_b_h($text, $level=2) { return '<!-- wp:heading {"level":'.$level.'} --><h'.$level.' class="wp-block-heading">'.esc_html($text).'</h'.$level.'><!-- /wp:heading -->'; }
function tc_b_p($html) {
    $html = preg_replace_callback('~href="(/[^\"]*)"~', fn($m) => 'href="'.esc_url(home_url(html_entity_decode($m[1]))).'"', $html);
    return '<!-- wp:paragraph --><p>'.wp_kses_post($html).'</p><!-- /wp:paragraph -->';
}
function tc_b_group($class,$body,$id='') { return '<!-- wp:group '.wp_json_encode(array_filter(['className'=>$class,'anchor'=>$id])).' --><div '.($id?'id="'.esc_attr($id).'" ':'').'class="wp-block-group '.esc_attr($class).'">'.$body.'</div><!-- /wp:group -->'; }
function tc_b_shortcode($text) { return '<!-- wp:shortcode -->'.$text.'<!-- /wp:shortcode -->'; }
function tc_b_real($item) {
    $plain=trim(html_entity_decode(wp_strip_all_tags($item)));
    return !preg_match('/^(?:\[|<(?:BUTTON|ICON|ACCORDION|DROPDOWN)>)/', $plain) && !in_array($plain,['Services','Why us?','Contact us','Contact Us','Blogs','Get started','Submit','View all articles','Get Your Complimentary IT Consult Today!'],true);
}
function tc_b_items($items) {
    $out=''; $label='';
    foreach($items as $item) {
        $plain=html_entity_decode(wp_strip_all_tags($item));
        if (preg_match('/^(?:<ICON>|\[ICON\])\s*(.*)/',$plain,$m)) { $label=$m[1]; continue; }
        if (!tc_b_real($item)) continue;
        $out.=tc_b_p(($label?'<strong>'.esc_html($label).'</strong><br>':'').$item); $label='';
    }
    return $out;
}
function tc_b_section($sections,$prefix) {
    foreach($sections as $section) if(str_starts_with($section['heading'],$prefix)) return $section;
    return null;
}
function tc_b_children($sections,$heading) {
    $result=[];$found=false;
    foreach($sections as $section) {
        if($section['heading']===$heading) { $found=true;continue; }
        if($found && $section['level']<=2) break;
        if($found) $result[]=$section;
    }
    return $result;
}
function tc_b_faq($sections) {
    $section=tc_b_section($sections,'Frequently Asked'); $out=tc_b_h($section['heading']);
    foreach(tc_b_children($sections,$section['heading']) as $faq) $out.='<!-- wp:details --><details class="wp-block-details"><summary>'.esc_html($faq['heading']).'</summary>'.tc_b_items($faq['items']).'</details><!-- /wp:details -->';
    return tc_b_group('faq-section brief-faq',$out);
}
function tc_b_photo($file,$class) { return '<!-- wp:image {"sizeSlug":"large","className":"'.$class.'"} --><figure class="wp-block-image size-large '.$class.'"><img src="'.esc_url(get_theme_file_uri('assets/'.$file)).'" alt="Illustrative IT environment" width="960" height="540" loading="lazy" decoding="async"/></figure><!-- /wp:image -->'; }
function tc_b_form($heading) { return tc_b_group('brief-consultation',tc_b_group('brief-consultation-intro',tc_b_h($heading).tc_b_p('Stuck with an IT issue? Get your complimentary IT consultation today.')).tc_b_shortcode('[techcloud_contact_form]')); }
function tc_b_save($post,$fields) {
    if(!metadata_exists('post',$post->ID,'_tc_before_client_brief')) update_post_meta($post->ID,'_tc_before_client_brief',['title'=>$post->post_title,'excerpt'=>$post->post_excerpt,'content'=>$post->post_content]);
    $result=wp_update_post(array_merge(['ID'=>$post->ID],$fields),true);
    if(is_wp_error($result)) WP_CLI::error($result->get_error_message());
}
$slugs=['managed-it-services','end-user-it-support','it-staffing','project-management','it-installation-infrastructure'];
$home_data=$data['pages'][0];$home_sections=$home_data['sections'];
$services_section=tc_b_section($home_sections,'Our IT Services');
$service_cards=tc_b_children($home_sections,$services_section['heading']);
foreach($service_cards as $i=>$card) {
    $post=get_page_by_path('services/'.$slugs[$i]);
    update_post_meta($post->ID,'_tc_service_label',$card['heading']);
    update_post_meta($post->ID,'_tc_service_summary',html_entity_decode(wp_strip_all_tags($card['items'][0])));
}
foreach($data['pages'] as $page) {
    $post=$page['path']==='/'?get_post(get_option('page_on_front')):get_page_by_path(trim($page['path'],'/'));
    if(!$post) WP_CLI::error('Missing page '.$page['path']);
    $sections=$page['sections'];
    update_post_meta($post->ID,'_tc_seo_title',$page['seo_title']);
    update_post_meta($post->ID,'_tc_seo_description',$page['description']);
    update_post_meta($post->ID,'_tc_client_brief_source',$data['source_sha256']);
    $intro=html_entity_decode(wp_strip_all_tags($sections[0]['items'][0]));
    if($page['path']==='/') {
        $about=tc_b_section($sections,'About TechCloud');
        $content=tc_b_group('about-panel brief-about',tc_b_group('about-intro',tc_b_photo('workplace.webp','workplace-photo').tc_b_h($about['heading']).tc_b_items($about['items'])));
        $content.=tc_b_group('section brief-services',tc_b_group('section-head',tc_b_h($services_section['heading']).tc_b_items($services_section['items'])).tc_b_shortcode('[techcloud_services]'),'Services');
        $why=tc_b_section($sections,'Why Choose'); $benefits='';
        foreach(tc_b_children($sections,$why['heading']) as $benefit) $benefits.=tc_b_group('benefit',tc_b_h($benefit['heading'],3).tc_b_items($benefit['items']));
        $content.=tc_b_group('section brief-why',tc_b_h($why['heading']).tc_b_group('brief-benefits',$benefits));
        // Keep approved case-study cards and the mobile process timeline.
        foreach(parse_blocks($post->post_content) as $block) {
            $serialized=serialize_block($block);
            if(str_contains($serialized,'[techcloud_cases') || str_contains($serialized,'process-grid')) $content.=$serialized;
        }
        $content.=tc_b_faq($sections).tc_b_form('Get Your Complimentary IT Consult Today!');
    } else {
        $definition=$sections[1];
        $images=['managed-it-services'=>'managed-it-poster.webp','end-user-it-support'=>'support-poster.webp','it-staffing'=>'workplace.webp','it-installation-infrastructure'=>'network-poster.webp'];
        $content=tc_b_group('service-detail-overview',tc_b_group('service-detail-intro',tc_b_h($definition['heading']).tc_b_items($definition['items'])).tc_b_photo($images[$post->post_name],'service-detail-photo'),'how');
        $scope=tc_b_section($sections,'Our Scope'); $rows='';
        foreach(tc_b_children($sections,$scope['heading']) as $capability) {
            if(str_starts_with($capability['heading'],'Stuck with')) continue;
            $rows.=tc_b_group('service-capability',tc_b_h($capability['heading'],3).tc_b_items($capability['items']));
        }
        $content.=tc_b_group('service-capabilities',tc_b_h($scope['heading']).tc_b_group('service-capability-list',$rows));
        $content.=tc_b_faq($sections);
        $consultation=tc_b_section($sections,'Get Your');
        $content.=tc_b_form($consultation['heading']);
        $related=tc_b_section($sections,'Other IT Solutions');$cards='';
        foreach(tc_b_children($sections,$related['heading']) as $item) {
            $title=$item['heading'];
            $slug=str_contains($title,'Managed')?'managed-it-services':(str_contains($title,'End User')?'end-user-it-support':(str_contains($title,'Staffing')?'it-staffing':(str_contains($title,'Project')?'project-management':'it-installation-infrastructure')));
            $cards.=tc_b_group('brief-related-card',tc_b_h($title,3).tc_b_items($item['items']).tc_b_p('<a href="/services/'.$slug.'/">Get started →</a>'));
        }
        $content.=tc_b_group('brief-related',tc_b_h($related['heading']).tc_b_group('brief-related-grid',$cards));
        $content=tc_b_group('service-detail brief-service',$content);
    }
    tc_b_save($post,['post_title'=>$page['heading'],'post_excerpt'=>$intro,'post_content'=>$content]);
}
// Bring the overview summaries into line with the client brief as well.
$overview=get_page_by_path('services');$rows='';
foreach($service_cards as $i=>$card) $rows.=tc_b_group('service-guide-row',tc_b_h($card['heading'],3).tc_b_items([$card['items'][0]]).tc_b_p('<a href="/services/'.$slugs[$i].'/">View service →</a>'));
$overview_content=tc_b_group('section',tc_b_shortcode('[techcloud_services]')).tc_b_group('service-guide',tc_b_h('Our IT Services in Singapore').tc_b_items($services_section['items']).$rows);
tc_b_save($overview,['post_content'=>$overview_content,'post_excerpt'=>html_entity_decode(wp_strip_all_tags($services_section['items'][0]))]);
update_option('tc_client_brief_20250303',$data['source_sha256']);
WP_CLI::success('Client content applied to Home, four services and the service overview. Previous copy retained.');
