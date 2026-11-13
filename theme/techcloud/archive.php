<?php get_header(); ?>
<main id="main" class="wrap blog-index">
    <header class="blog-intro"><a class="blog-back" href="<?php echo esc_url(home_url('/blog/')); ?>">← All blogs</a><h1><?php the_archive_title(); ?></h1><?php the_archive_description(); ?></header>
    <section class="blog-listing" aria-label="Blog articles">
        <?php if (have_posts()) : ?>
            <div class="blog-grid"><?php while (have_posts()) : the_post(); tc_blog_card(); endwhile; ?></div>
            <?php the_posts_pagination(['prev_text' => '← Previous', 'next_text' => 'Next →']); ?>
        <?php else : ?><p>No articles here yet.</p><?php endif; ?>
    </section>
</main>
<?php get_footer(); ?>
