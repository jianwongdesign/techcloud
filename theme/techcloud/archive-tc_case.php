<?php get_header(); ?>
<main id="main" class="wrap"><section class="page-hero"><h1>Technology in practice.</h1><p class="hero-description">A closer look at the challenges we help businesses solve.</p></section><section class="section case-archive"><?php if (have_posts()) : ?><div class="case-grid"><?php while (have_posts()) : the_post(); ?><?php tc_case_card("h2"); ?><?php endwhile; ?></div><?php the_posts_pagination(); else : echo tc_case_list(); endif; ?></section><?php echo tc_cta(); ?></main>
<?php get_footer(); ?>
