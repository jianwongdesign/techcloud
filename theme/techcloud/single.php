<?php get_header(); ?>
<main id="main" class="blog-single wrap">
<?php while (have_posts()) : the_post(); ?>
    <article <?php post_class('blog-article'); ?>>
        <a class="blog-back" href="<?php echo esc_url(home_url('/blog/')); ?>">← Back to blog</a>
        <header class="blog-hero">
            <div class="blog-hero-media"><?php tc_blog_image('large', true); ?></div>
            <div class="blog-hero-copy">
                <span class="eyebrow">TechCloud insights</span>
                <h1><?php the_title(); ?></h1>
                <time class="blog-date" datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date('j F Y')); ?></time>
            </div>
        </header>
        <div class="blog-body">
            <?php the_content(); wp_link_pages(['before' => '<nav class="blog-content-pages" aria-label="Article pages">Pages: ', 'after' => '</nav>']); ?>
        </div>
    </article>
    <?php $related_ids = tc_related_blog_ids(get_the_ID()); ?>
    <?php if ($related_ids) : $related = new WP_Query(['post_type' => 'post', 'post_status' => 'publish', 'post__in' => $related_ids, 'orderby' => 'post__in', 'posts_per_page' => 3, 'ignore_sticky_posts' => true, 'no_found_rows' => true]); ?>
        <section class="blog-related" aria-labelledby="related-blogs-title">
            <h2 id="related-blogs-title">Related Blogs</h2>
            <div class="blog-grid"><?php while ($related->have_posts()) : $related->the_post(); tc_blog_card('h3'); endwhile; ?></div>
        </section>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
