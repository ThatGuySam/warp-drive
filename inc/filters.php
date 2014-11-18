<?php 

/*

Filters

*/


function insert_luminance_data($post_ID) {
    $src = wp_get_attachment_image_src( $post_ID, 'thumb-hd' )[0];
    $lum = get_avg_luminance($src, 10, true);
    add_post_meta( $post_ID, 'image_lum', $lum, true ) || update_post_meta( $post_ID, 'image_lum', $lum );
    return $post_ID;
}

//add_filter('add_attachment', 'insert_luminance_data', 10, 2);

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


/* Custom Menu Classes */

remove_filter('nav_menu_css_class', 'roots_nav_menu_css_class', 10);

function roots_custom_nav_menu_css_class($classes, $item) {
	
	$menu_locations = get_nav_menu_locations();
	
	
	
	$slug = sanitize_title($item->title);
	$classes = preg_replace('/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', 'active', $classes);
	$classes = preg_replace('/^((menu|page)[-_\w+]+)+/', '', $classes);
	
	if ( has_term($menu_locations['primary_navigation'], 'nav_menu', $item) ) {
		$classes[] = 'col-xs-2 hidden-xs nopadding menu-item menu-' . $slug;
	}
	
	$classes = array_unique($classes);
	
	return array_filter($classes, 'is_element_empty');
}
add_filter('nav_menu_css_class', 'roots_custom_nav_menu_css_class', 10, 2);


//Add extra css classes to menu items
function expanded_nav_menu_css_class( $classes = array(), $item, $args ) {
    $location_name = 'expanded_navigation';
    static $top_level_count = 0; //Top level menu items counter
 
    if($args->theme_location== $location_name){ //Limit to this menu location only
        
        if($item->menu_item_parent==0 and $top_level_count!==null){ //Count top level menu items
            $top_level_count++; //Increment
        }
        
        if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $location_name ] ) ) {
            $main_nav = wp_get_nav_menu_object( $locations[ $location_name ] );
				
			if( $item->menu_item_parent==0 ){
				$classes[] = 'col-lg-2 col-md-3 col-sm-4 col-xs-6';
			}
			
            if ($item->menu_order == 1) {
                $classes[] = 'col-lg-offset-1 menu-item-first'; //First menu item
            }
            if($top_level_count==count_top_level_menu_items($main_nav->term_id)){
                $classes[] = 'menu-item-last'; //Last top level menu item
                $top_level_count = null; //Disable our counter, no need for it
            }
        }
    }
    return $classes;
}
add_filter( 'nav_menu_css_class', 'expanded_nav_menu_css_class', 10, 3 );




/* Outer Menu Buttons */

function add_outer_menu($items, $args) {
 
 // If this isn't the main navbar menu, do nothing
 if( !($args->theme_location == 'primary_navigation') )
 return $items;
 
	ob_start(); ?>
		
	<li class="menu-outer-item col-xs-1">
      <a href="<?php echo esc_url(home_url('/')); ?>"><i class="icon-guts-g"></i></a>
	</li>
		    
	<?php $before = ob_get_clean();
	
	
	ob_start(); ?>
		
	<li class="menu-outer-item search-toggle col-xs-1 pull-right">
		<a href="#">
			<i class="fa fa-search "></i>
		</a>
	</li>
		    
	<?php $after = ob_get_clean();
		
		
 
 // On main menu: put styling around search and append it to the menu items
 return 
 	$before . $items . $after;
 	
}
add_filter('wp_nav_menu_items', 'add_outer_menu', 10, 2);



/*

Actions

*/

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



/**
 * Manage google fonts of load_google_font()
 * set GOOGLE_FONTS constant in config.php
 */
function load_google_fonts() {
  if( ! defined( 'GOOGLE_FONTS' ) ) return;
  echo '<link href="http://fonts.googleapis.com/css?family=' . GOOGLE_FONTS . '" rel="stylesheet" type="text/css" />'."\n";
}
add_action( 'wp_head', 'load_google_fonts' , 1);


//Custom Admin CSS
add_action('admin_head', 'my_custom_css');

function my_custom_css() {
  echo '<link rel="stylesheet" type="text/css" href="'.get_template_directory_uri() . '/assets/css/admin.css'.'">';
  //get_template_directory_uri() . '/css/responsive.css'
}