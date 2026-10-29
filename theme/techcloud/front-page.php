<?php get_header(); ?>
<main id="main" class="wrap">
<?php while (have_posts()) : the_post(); ?>
<section class="home-hero">
    <div class="hero-copy"><p class="eyebrow"><span class="status-dot"></span> YOUR IT PARTNER IN SINGAPORE</p><h1><?php the_title(); ?></h1><p class="hero-description"><?php echo esc_html(get_the_excerpt()); ?></p><div class="hero-actions"><a class="button" href="<?php echo esc_url(home_url('/contact/')); ?>">Let’s talk IT <?php echo tc_arrow(); ?></a><a class="text-link" href="<?php echo esc_url(home_url('/services/')); ?>">Explore our services <span aria-hidden="true">↓</span></a></div></div>
    <div class="hero-art" role="img" aria-label="Connected technology, centred around your business"><div class="art-grid"></div><div class="orbit orbit-one"></div><div class="orbit orbit-two"></div><div class="orbit orbit-three"></div><div class="cloud-core"><svg viewBox="0 0 120 84" aria-hidden="true"><path d="M31 65h55a19 19 0 0 0 2-38A29 29 0 0 0 34 20 23 23 0 0 0 31 65Z" fill="none" stroke="currentColor" stroke-width="2.5"/><path d="M60 36v45m-10-10 10 10 10-10" fill="none" stroke="currentColor" stroke-width="2.5"/></svg></div><span class="node node-one"></span><span class="node node-two"></span><span class="node node-three"></span><span class="art-label label-one">People.</span><span class="art-label label-two">Systems.</span><span class="art-label label-three">Possibilities.</span><span class="art-caption">CONNECTED. SUPPORTED. READY.</span><span class="art-cross">+</span></div>
</section>
<div class="intro-strip"><span>A little less complexity.<br><strong>A lot more peace of mind.</strong></span><span>Managed IT</span><span>People-first support</span><span>Room to grow</span></div>
<div class="page-content"><?php the_content(); ?></div>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
