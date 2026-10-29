<?php get_header(); ?>
<main id="main" class="wrap section"><h1><?php echo is_search() ? esc_html('Search results for: ' . get_search_query()) : 'Latest updates'; ?></h1><?php if (have_posts()) : while (have_posts()) : the_post(); ?><article><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></article><?php endwhile; the_posts_pagination(); else : ?><p>Nothing here yet.</p><?php endif; ?></main>
<?php get_footer(); ?>
