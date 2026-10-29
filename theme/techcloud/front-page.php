<?php get_header(); ?>
<main id="main">
<?php while (have_posts()) : the_post(); ?>
<?php $has_hero_video = file_exists(get_theme_file_path('assets/hero-motion.mp4')); ?>
<section class="home-hero wrap motion-media">
    <div class="hero-layout">
        <div class="hero-copy">
            <h1><?php the_title(); ?></h1>
            <p class="hero-description"><?php echo esc_html(get_the_excerpt()); ?></p>
            <div class="hero-actions"><a class="button" href="<?php echo esc_url(home_url('/contact/')); ?>">Discuss your IT needs <?php echo tc_arrow(); ?></a><a class="text-link" href="<?php echo esc_url(home_url('/services/')); ?>">View services <?php echo tc_arrow(); ?></a></div>
        </div>
    </div>
    <div class="hero-photo">
        <?php if ($has_hero_video) : ?>
        <video muted loop playsinline preload="none" poster="<?php echo esc_url(has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'full') : get_theme_file_uri('assets/hero-motion-poster.webp')); ?>" data-src="<?php echo esc_url(get_theme_file_uri('assets/hero-motion.mp4')); ?>" aria-label="Illustrative technology infrastructure video"></video>
        <?php elseif (has_post_thumbnail()) : the_post_thumbnail('full', ['fetchpriority' => 'high']); else : ?>
        <img src="<?php echo esc_url(get_theme_file_uri('assets/infrastructure.webp')); ?>" width="1536" height="1024" alt="Illustrative technology infrastructure adjoining a modern workplace" fetchpriority="high">
        <?php endif; ?>
    <?php if ($has_hero_video) : ?>
    <button class="motion-control hero-motion-control" type="button" hidden aria-label="Play technology infrastructure video">Play video</button>
    <?php endif; ?>
    </div>
</section>
<div class="wrap">
<div class="page-content"><?php the_content(); ?></div>
</div>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
