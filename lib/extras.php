<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Config;

/**
 * Add <body> classes
 */
function body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }

  // Add class if sidebar is active
  if (Config\display_sidebar()) {
    $classes[] = 'sidebar-primary';
  }

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');


/* 
	Dominate Stuff
*/


date_default_timezone_set('America/Chicago');


include get_theme_root().'/'.get_template().'/inc/snippets.php';

include get_theme_root().'/'.get_template().'/inc/filters.php';

include get_theme_root().'/'.get_template().'/inc/heroes.php';

include get_theme_root().'/'.get_template().'/inc/boxes.php';

include get_theme_root().'/'.get_template().'/inc/organisms.php';
