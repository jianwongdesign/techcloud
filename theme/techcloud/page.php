<?php get_header(); ?>
<main id="main" class="wrap">
<?php while (have_posts()) : the_post();
    $brief_service = get_post_meta(get_the_ID(), '_tc_client_brief_source', true) && wp_get_post_parent_id(get_the_ID());
?>
<section class="page-hero<?php echo $brief_service ? ' brief-page-hero' : ''; ?>">
<div class="page-hero-breadcrumb"><a class="breadcrumb" href="<?php echo esc_url(home_url('/')); ?>">Home</a><span class="breadcrumb"> / <?php if (wp_get_post_parent_id(get_the_ID())) : ?><a href="<?php echo esc_url(get_permalink(wp_get_post_parent_id(get_the_ID()))); ?>">Services</a> / <?php endif; ?><?php echo esc_html(get_the_title()); ?></span></div>
<?php if ($brief_service) : ?><div class="brief-hero-layout"><?php endif; ?>
<h1><?php the_title(); ?></h1>
<?php if ($brief_service) : ?><div class="brief-hero-intro"><?php endif; ?>
<?php if (has_excerpt()) : ?><p class="hero-description"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
<?php if ($brief_service) : ?><a class="text-link brief-learn" href="#how">Learn How <?php echo tc_arrow(); ?></a></div></div><?php endif; ?>
</section>
<div class="page-content"><?php the_content(); ?></div>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
