<?php
/**
 * Clean up the_excerpt()
 */
function roots_excerpt_more($more) {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'roots') . '</a>';
}
add_filter('excerpt_more', 'roots_excerpt_more');

/**
 * Manage output of wp_title()
 */
function roots_wp_title($title) {
  if (is_feed()) {
    return $title;
  }

  $title .= get_bloginfo('name');

  return $title;
}
add_filter('wp_title', 'roots_wp_title', 10);


function show_sitemap() {
  if( isset($_GET['show_sitemap'])) {
    $the_query = new WP_Query(array('post_type' => 'any', 'posts_per_page' => '-1', 'post_status' => 'publish'));
    $urls = array();
    while($the_query->have_posts()) {
      $the_query->the_post();
      $urls[] = get_permalink();
    }
    die(json_encode($urls));
  }
}
add_action('template_redirect', 'show_sitemap');



/*

Menu

*/


remove_filter('nav_menu_css_class', 'roots_nav_menu_css_class', 10);

function roots_custom_nav_menu_css_class($classes, $item) {
  $slug = sanitize_title($item->title);
  $classes = preg_replace('/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', 'active', $classes);
  $classes = preg_replace('/^((menu|page)[-_\w+]+)+/', '', $classes);

  $classes[] = 'col-sm-2 menu-' . $slug;

  $classes = array_unique($classes);

  return array_filter($classes, 'is_element_empty');
}
add_filter('nav_menu_css_class', 'roots_custom_nav_menu_css_class', 10, 2);



function add_search_form_to_menu($items, $args) {
 
 // If this isn't the main navbar menu, do nothing
 if( !($args->theme_location == 'primary_navigation') )
 return $items;
 
 // On main menu: put styling around search and append it to the menu items
 return $items . '<li class="search-toggle col-sm-1"></li>';
}
add_filter('wp_nav_menu_items', 'add_search_form_to_menu', 10, 2);