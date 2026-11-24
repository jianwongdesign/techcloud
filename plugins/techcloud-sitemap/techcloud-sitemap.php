<?php
/**
 * Plugin Name: TechCloud Sitemap
 * Description: Excludes author archives from the public XML sitemap.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

add_filter('wp_sitemaps_add_provider', static function ($provider, $name) {
    return $name === 'users' ? false : $provider;
}, 10, 2);
