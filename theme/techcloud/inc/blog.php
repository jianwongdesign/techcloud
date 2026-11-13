<?php
defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', function () {
    if (is_home() || is_singular('post') || is_category() || is_tag() || is_date() || is_author()) {
        wp_enqueue_style('techcloud-blog', get_theme_file_uri('assets/blog.css'), ['techcloud-rich'], filemtime(get_theme_file_path('assets/blog.css')));
    }
}, 20);

add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_home()) {
        $query->set('posts_per_page', 6);
        $query->set('ignore_sticky_posts', true);
    }
});

function tc_blog_image($size = 'large', $hero = false) {
    if (has_post_thumbnail()) {
        the_post_thumbnail($size, ['class' => 'blog-image', 'loading' => $hero ? 'eager' : 'lazy', 'decoding' => 'async']);
    } else {
        // Neutral brand artwork when an editor has not selected a featured image.
        echo '<div class="blog-image blog-image-fallback" aria-hidden="true"><img src="' . esc_url(get_theme_file_uri('assets/logo.png')) . '" alt="" width="2560" height="1158"></div>';
    }
}

function tc_blog_card($heading = 'h2') {
    $heading = $heading === 'h3' ? 'h3' : 'h2';
    ?>
    <article <?php post_class('blog-card'); ?>>
        <a class="blog-card-link" href="<?php the_permalink(); ?>">
            <div class="blog-card-media"><?php tc_blog_image(); ?></div>
            <div class="blog-card-copy">
                <time class="blog-date" datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date('j F Y')); ?></time>
                <<?php echo $heading; ?>><?php the_title(); ?></<?php echo $heading; ?>>
                <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 25)); ?></p>
                <span class="blog-card-more">Read article <span aria-hidden="true"><?php echo tc_arrow(); ?></span></span>
            </div>
        </a>
    </article>
    <?php
}

function tc_related_blog_ids($post_id) {
    $common = ['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3, 'post__not_in' => [$post_id], 'ignore_sticky_posts' => true, 'no_found_rows' => true, 'fields' => 'ids', 'orderby' => ['date' => 'DESC', 'ID' => 'DESC']];
    $categories = wp_get_post_categories($post_id);
    $ids = $categories ? get_posts(array_merge($common, ['category__in' => $categories])) : [];
    if (count($ids) < 3) {
        $ids = array_merge($ids, get_posts(array_merge($common, ['posts_per_page' => 3 - count($ids), 'post__not_in' => array_merge([$post_id], $ids)])));
    }
    return $ids;
}
