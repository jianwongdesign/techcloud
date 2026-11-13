<?php get_header(); ?>
<main id="main" class="blog-index">
    <header class="blog-intro wrap">
        <p class="eyebrow">The TechCloud Blog</p>
        <h1>Ideas for a better<br>connected business.</h1>
        <p class="blog-intro-description">Insights on IT support, infrastructure and keeping your business moving. Practical perspectives for the decisions ahead.</p>
    </header>
    <section class="blog-listing wrap" aria-label="Blog articles">
        <?php if (have_posts()) : ?>
            <div class="blog-grid"><?php while (have_posts()) : the_post(); tc_blog_card(); endwhile; ?></div>
            <?php the_posts_pagination(['mid_size' => 1, 'prev_text' => '← Previous', 'next_text' => 'Next →', 'screen_reader_text' => 'Blog pagination', 'aria_label' => 'Blog pages']); ?>
        <?php else : ?>
            <div class="blog-empty"><h2>More to share, soon.</h2><p>Our latest articles will appear here. In the meantime, explore how we can support your business.</p><a class="text-link" href="<?php echo esc_url(home_url('/services/')); ?>">Explore our services <?php echo tc_arrow(); ?></a></div>
        <?php endif; ?>
    </section>
</main>
<?php get_footer(); ?>
