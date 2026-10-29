<!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body id="top" <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header wrap">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="TechCloud home"><img src="<?php echo esc_url(get_theme_file_uri('assets/logo.png')); ?>" width="2560" height="1158" alt="TechCloud"></a>
    <button class="menu-toggle" aria-expanded="false" aria-controls="primary-nav"><span>Menu</span><span class="menu-lines" aria-hidden="true"></span></button>
    <nav id="primary-nav" class="primary-nav" aria-label="Primary navigation">
        <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav-list', 'fallback_cb' => 'tc_default_menu', 'depth' => 1]); ?>
        <a class="shop-link" href="<?php echo esc_url(tc_shop_url()); ?>">Shop <?php echo tc_arrow(); ?></a>
    </nav>
</header>
