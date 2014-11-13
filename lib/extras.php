<?php

require get_theme_root().'/'.get_template().'/lib/vendor/autoload.php';

use League\ColorExtractor\Client as ColorExtractor;

$client = new ColorExtractor;

function debug( $this ) {
	
	ob_start(); ?>
	
		<pre>
			<?php print_r($this); ?>
		</pre>
	
	<?php $output = ob_get_clean();
	
	echo $output;
	
}

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


add_image_size( '1080', 1920, 1080, true );
add_image_size( '720', 1280, 720, true );
add_image_size( '360', 640, 360, true );
add_image_size( 'thumb-hd', 400, 225, true );



function get_avg_luminance($filename, $num_samples=10) {
    $img = imagecreatefromjpeg($filename);
 
    $width = imagesx($img);
    $height = imagesy($img);
 
    $x_step = intval($width/$num_samples);
    $y_step = intval($height/$num_samples);
 
    $total_lum = 0;
 
    $sample_no = 1;
 
    for ($x=0; $x<$width; $x+=$x_step) {
        for ($y=0; $y<$height; $y+=$y_step) {
 
            $rgb = imagecolorat($img, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
 
            // choose a simple luminance formula from here
            // http://stackoverflow.com/questions/596216/formula-to-determine-brightness-of-rgb-color
            $lum = ($r+$r+$b+$g+$g+$g)/6;
 
            $total_lum += $lum;
 
            $sample_no++;
        }
    }
 
    // work out the average
    $avg_lum  = $total_lum/$sample_no;
    return $avg_lum;
    // assume a medium gray is the threshold, #acacac or RGB(172, 172, 172)
    // this equates to a luminance of 170
}

function insert_luminance_data($post_ID) {
    $src = wp_get_attachment_image_src( $post_ID, 'thumb-hd' )[0];
    $lum = get_avg_luminance($src, 10, true);
    add_post_meta( $post_ID, 'image_lum', $lum, true ) || update_post_meta( $post_ID, 'image_lum', $lum );
    return $post_ID;
}

//add_filter('add_attachment', 'insert_luminance_data', 10, 2);



/**
 * Manage google fonts of load_google_font()
 * set GOOGLE_FONTS constant in config.php
 */
function load_google_fonts() {

  if( ! defined( 'GOOGLE_FONTS' ) ) return;

  echo '<link href="http://fonts.googleapis.com/css?family=' . GOOGLE_FONTS . '" rel="stylesheet" type="text/css" />'."\n";

}

add_action( 'wp_head', 'load_google_fonts' , 1);


/* Plugin Deregisters to avoid redendancy after script concatenation */
add_action('wp_print_styles', 'deregister_styles', 100);

function deregister_styles() {
  //Visual Composer
  if( wp_style_is( 'js_composer_front', 'registered' ) )		wp_deregister_style('js_composer_front');
  if( wp_style_is( 'js_composer_custom_css', 'registered' ) )	wp_deregister_style('js_composer_custom_css');
  if( wp_script_is( 'wpb_composer_front_js', 'registered' ) )	wp_deregister_script('wpb_composer_front_js');
}



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

register_nav_menus(array(
	'expanded_navigation' => __('Expanded Navigation')
));

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
 
//Function to count the total number of top level menus. Useful for menus with sub menus
function count_top_level_menu_items($menu_id){
    $count = 0;
    $menu_items = wp_get_nav_menu_items($menu_id);
    foreach($menu_items as $menu_item){
        if($menu_item->menu_item_parent==0){
            $count++;
        }
    }
    return $count;
}


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




//Custom Admin CSS
add_action('admin_head', 'my_custom_css');

function my_custom_css() {
  echo '<link rel="stylesheet" type="text/css" href="'.get_template_directory_uri() . '/assets/css/admin.css'.'">';
  //get_template_directory_uri() . '/css/responsive.css'
}


$normalizeChars = array(
    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
    'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
    'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
    'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
);

function getSMS( $trigger_key ){
	
	$replies_field = get_field('replies', "8668");
	
	$replies = array();
	
	foreach ($replies_field as $key => $value) {
		$trigger = $replies_field[$key]['trigger'];
		$trigger = preg_replace("/[^A-Za-z ]/", '', $trigger);
		$replies[$trigger] = $replies_field[$key]['response'];
	}
	
	$replies = array_change_key_case($replies, CASE_LOWER);
	
//	$trigger_key = strtolower($_REQUEST['Body']);
	
	
	if ( array_key_exists($trigger_key, $replies) ) {
    	$message = $replies[$trigger_key];
	} else {
		$message = "Please retype and try again";
	}
	
	
	
	return $message;
}


function sendSMS($trigger_key, $number) {

	require('static/sms/Services/Twilio.php'); 
 
	$account_sid = get_site_option( 'twilio_sid' );
	$auth_token = get_site_option( 'twilio_tkn' );
	$client = new Services_Twilio($account_sid, $auth_token);
	
	$message = getSMS( $trigger_key );
	 
	$output = $client->account->messages->create(array( 
		'To' => "+1".$number, 
		'From' => "+19182233950", 
		'Body' => $message,   
	));

}

function sendMessageSMS($message, $number) {

	require('static/sms/Services/Twilio.php'); 
 
	$account_sid = get_site_option( 'twilio_sid' );
	$auth_token = get_site_option( 'twilio_tkn' );
	$client = new Services_Twilio($account_sid, $auth_token);
	
//	$message = getSMS( $trigger_key );
	 
	$output = $client->account->messages->create(array( 
		'To' => "+1".$number, 
		'From' => "+19182233950", 
		'Body' => $message,   
	));

}



function parse_title($unparsed) {
	$parsed = strtr ( $unparsed , array ('|' => '<br />'));
	return $parsed;
}

function ago($time){
	
	$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");
	
	$now = time();
	
	   $difference     = $now - $time;
	   $tense         = "ago";
	
	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
	   $difference /= $lengths[$j];
	}
	
	$difference = round($difference);
	
	if($difference != 1) {
	   $periods[$j].= "s";
	}
	
	$output = "$difference $periods[$j] ago";
	
	if( $time > strtotime('-14 day') ) $output = "Last week";
	
	if( $time > strtotime('-7 day') ) $output = "This week";
	
	return $output;
}

function getJson($url) {
    // cache files are created like cache/abcdef123456...
    $cacheFile = 'cache' . DIRECTORY_SEPARATOR . "json-" . md5($url);
	
	if( array_key_exists( 'purge' , $_GET ) ) unlink($cacheFile);
	
    if (file_exists($cacheFile)) {
        $fh = fopen($cacheFile, 'r');
        $cacheTime = trim(fgets($fh));
        
        //$cacheTime = filemtime($cacheFile);//We'll meet again

        // if data was cached recently, return cached data
        if ($cacheTime > strtotime('-60 minutes')) {
        	//echo $fh;
        	//echo file_get_contents($cacheFile);
            //return fread($fh);
            $json_cached = file_get_contents($cacheFile);
			
            return preg_replace('/^.+\n/', '', $json_cached);
        }

        // else delete cache file
        fclose($fh);
        unlink($cacheFile);
    }

    $json = file_get_contents($url);
	
    $fh = fopen($cacheFile, 'w');
    fwrite($fh, time() . "\n");
    fwrite($fh, $json);
    fclose($fh);

    return $json;
}


function heroOrganism($hero){
		
		switch ($hero->kind) {
			case "text":
				
				ob_start(); ?>
					
					
					<div class="container">
						<div class="page-header">
							<h1>
							<?php echo roots_title(); ?>
							</h1>
						</div>
					</div>
				
				<?php $hero->output = ob_get_clean();
				
			break;
			case "media":
			
				ob_start(); ?>
				
				<div class="hero-slide" >
				
					<img <?php echo $hero->srcType; ?>="<?php echo $hero->src; ?>" alt="<?php echo roots_title(); ?> Image">
					
					<div class="foreground ir">
					
						<div class="container">
							<div class="page-header">
								<h1>
									<?php echo do_shortcode( $hero->text ); ?>
								</h1>
							</div>
						</div>
						
					</div>
					
				</div>
				
				<?php $hero->output = ob_get_clean();
				
				
				//Link Wrapper
				if( $hero->link !== "" ): ob_start(); ?>
					
					<a href="<?php echo $hero->link; ?>" >
					
						<?php echo $hero->output; ?>
						
					</a>
					
				<?php $hero->output = ob_get_clean(); endif;
				

				
			break;
			case "video":
			//code to be executed if n=label2;
			break;
			case "shortcode":
				ob_start(); ?>
				
				
				
					<img src="<?php echo $hero->src; ?>" alt="<?php echo roots_title(); ?> Image">
					
					
					<div class="container">
						<div class="page-header">
							<h1>
							<?php echo roots_title(); ?>
							</h1>
							<div class="read-more">
								<a href="#content" class="scrollto">
									<span>More</span>
									<br>
									<span><i class="fa fa-angle-down fa-2x"></i></span>
								</a>
							</div>
						</div>
					</div>
					
					
				
				<?php $hero->output = ob_get_clean();
			break;
			default:
		}
		
		
		ob_start(); ?>
			<div class="hero-organism">
				<?php echo $hero->output; ?>
			</div>
		<?php $hero->output = ob_get_clean();
		
		
		return $hero->output;
	}


function boxesInstagram($user_id) {

	$boxes = array();

	function fetchData($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		$result = curl_exec($ch);
		curl_close($ch); 
		return $result;
	}
	
	$result = fetchData("https://api.instagram.com/v1/users/".$user_id."/media/recent/?access_token=10392439.44b554e.ab889407055b456381897320866ab6b7");
	$result = json_decode($result);
	
	$i=0;
	foreach ($result->data as $post) {
		//debug($post);
		
		$boxes[$i]['type'] 		= $post->type;
		$boxes[$i]['id'] 		= $post->id;
		$boxes[$i]['image_url'] = $post->images->low_resolution->url;
		//$boxes[$i]['date'] 		= date( "D n/j" , strtotime( $post->created_time ) );
		$boxes[$i]['text'] 		= $post->caption->text;
		$boxes[$i]['link'] 		= $post->link;
		
		$i++;
	}
	
	return $boxes;
	
	
}


function boxesVimeo($boxes) {

	$output = array();
	
	$vid_url = parse_url($boxes->source);
	
	$json_url = 'http://vimeo.com/api/v2'.$vid_url['path'].'/videos.json';
	
	$json = json_decode( getJson($json_url) );
	
	$i=0;
	foreach ($json as $post) {
		//debug( $post );
		
		$details = new stdClass;
		
		$desc = explode('<br />', $post->description);
		
		$d = 0;
		foreach( $desc as $val ) {
			
			//if is not first line and contains dash
			
			if( $d && strpos($val,'|') !== false ) {
				
				$vals = explode('|', $val);
				
				$dkey = strtolower( trim( $vals[0] ) );
				
				$dval = trim( $vals[1] ); 
				
				$details->$dkey = $dval;
			}
			
			$d++;
		}
		
		if( isset($details->date) ){
			$date = date( "F jS" , strtotime( $details->date ) );
		} else {
			$date = date( "F jS" , strtotime( $post->upload_date ) );
		}
		
		$title = $post->title;
		
		if( get_page_template_slug() !== "page-watch.php" ) {
			$link = "/watch/?vid=".$post->id;
		} else {
			$link = "#".$post->id;
		}
		
		
		$output[$i]['type'] 		= "video";
		$output[$i]['id'] 		= $post->id;
		$output[$i]['image_url'] = $post->thumbnail_large;
		$output[$i]['date'] 		= $date;
		$output[$i]['title'] 	= $title;
		$output[$i]['text'] 		= $title;
		$output[$i]['desc'] 		= $desc[0];
		$output[$i]['link'] 		= $link;
		
		if( $i >= $boxes->amount - 1 ) break;
		
		$i++;
	}
	
	return $output;
	
}



class Boxes {
	static $add_script;

	static function init() {
		add_shortcode('boxes', array(__CLASS__, 'handle_shortcode'));

		add_action('init', array(__CLASS__, 'register_script'));
		
		add_action('wp_footer', array(__CLASS__, 'print_script'));
		
		add_action('wp_footer', array(__CLASS__, 'internal_script') );
	}

	static function handle_shortcode($atts) {
		self::$add_script = true;
		
		extract( shortcode_atts( array(
			'type'		=> false,
			'source'	=> false,
			'class'		=> false,
			'amount'	=> 8,
			'show'		=> 3,
		), $atts, 'boxes' ) );
		
		$boxes = new stdClass();
		
		foreach ($atts as $key => $value) {$boxes->$key = $value; }//Convert Shortcode attributes to object values
		
		$parsed_classes = explode(' ', $class);
		
		$props = array();
		
		foreach( $parsed_classes as $prop ) {
			$props[$prop] = true;
		}
		
		$boxes->props = $props;
		
		
		//Feed Type
		switch ($type) {
		    case "instagram":
		        $boxes->boxes = boxesInstagram($boxes);
		        
		        $target = "_blank";
		        
		        break;
		    case "vimeo":
		        $boxes->boxes = boxesVimeo($boxes);
		        
		        $vid_url = parse_url($boxes->source);
		        $boxes->id = preg_replace('/[^\da-z]/i', '', $vid_url['path'] );
		        
		        $target = "_self";
		        
		        break;
		    case "events":
		        echo "events!";
		        break;
		    case "category":
		        echo $source;
		        break;
		    case "youtube":
		        echo $source;
		        break;
		    case "acf":
		        echo $source;
		        break;
		    default:
		       echo "Nothing here";
		}		
		
		//debug( $boxes );
		
		ob_start();
		?>
			<div id="boxes-<?php echo $type; ?>-<?php echo $boxes->id; ?>" class="boxes boxes-<?php echo $type; ?> <?php echo $class; ?>">


			<div class="frame" data-show="<?php echo $boxes->show; ?>">
				<ul class="easecubic">
					<?php foreach($boxes->boxes as $box){ ?>
					
						<?php 
							if( isset( $props['date-format-human'] ) ){
								
								$normal_date = strtotime( $box['date'] );
								
								if( strtotime( $box['text'] ) ) $normal_date = strtotime( $box['text'] );
								
								$box['text'] = ago( $normal_date );
							}
						?>
	
						
						<li id="box-<?php echo $type; ?>-<?php echo $box['id']; ?>" class="box-<?php echo $box['type']; ?> easecubic">
							
							<a href="<?php echo $box['link']; ?>" target="<?php echo $target; ?>" >
								<div class="box-image">
									<img class="easecubic" data-lazy="<?php echo $box['image_url']; ?>" alt="<?php echo $box['text']; ?>" >
								</div>
								
								<div class="box-header easecubic">
									
									<div class="box-header-content">
										<h3><?php echo parse_title( $box['title'] ); ?></h3>
										<div class="box-date easecubic"><?php echo $box['date']; ?></div>
									</div>
								</div>
								
								
							
								<div class="box-caption easecubic"><p><?php echo parse_title( $box['desc'] ); ?></p></div>
								
								
							</a>
							
						</li>
						
					<?php } ?>
				</ul>
			</div>
			
		</div>
		<?php
		$content = ob_get_clean();
			
		return $content;
	}

	static function register_script() {
		//wp_register_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css', array(), '4.1.0', 'screen' );
		//wp_register_script('imagesloaded', '//cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/3.0.4/jquery.imagesloaded.js', array('jquery'), '3.0.4', true);
	}

	static function print_script() {
		if ( ! self::$add_script )
			return;
			//wp_print_styles('font-awesome');
			//wp_print_scripts('imagesloaded');
	}
	
	static function internal_script() {
		if ( ! self::$add_script )
			return;
/*
		?>
			
			<script type="text/javascript">
				if ( undefined !== window.jQuery ) { jQuery(function ($) { 'use strict';
					
					

				}); }
			</script>
			
			<style>
				
				@media only screen and (max-width: 480px), screen and (max-device-width: 480px), screen and (max-width: 700px)  {
					.frame > ul {
						-webkit-transition: none !important;
						transition: none !important;
					}
				}
				
			</style>
		<?php
*/
	}
}

Boxes::init();




class SMS {
	static $add_script;

	static function init() {
		add_shortcode('sms', array(__CLASS__, 'handle_shortcode'));

		add_action('init', array(__CLASS__, 'sms_register_script'));
		add_action('wp_footer', array(__CLASS__, 'sms_print_script'));
		
		add_action('wp_footer', array(__CLASS__, 'sms_internal_script') );
	}

	static function handle_shortcode($atts) {
		self::$add_script = true;
		
		extract( shortcode_atts( array(
			'key' => false,
		), $atts, 'sms' ) );
		
		
		ob_start();
		?>
			<div class="phone-share mode-closed" data-key="<?php echo $key; ?>">
				<input class="phone-box" type="text" placeholder="(918) 555-4422"/>
				<span class="inner-button"></span>
				<span class="front-button" data-message="Share to Phone"></span>
			</div>
		<?php
		$content = ob_get_clean();
			
		return $content;
	}

	static function sms_register_script() {
		//js
		wp_register_script('masked-input', '//cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.3.1/jquery.maskedinput.min.js', array('jquery'), '1.3.1', true);
	}

	static function sms_print_script() {
		if ( ! self::$add_script )
			return;
			
			//JS
			wp_print_scripts('masked-input');
	}
	
	static function sms_internal_script() {
		if ( ! self::$add_script )
			return;
		?>
			
			<script type="text/javascript">
				if ( undefined !== window.jQuery ) { jQuery(function ($) { 'use strict';
					
					var selectorStart = "mode-";
					
					var status = "closed";
					
					$.fn.findSelector = function(str, strArray) {
					    for (var j=0; j<strArray.length; j++) {
					        if (strArray[j].substring(0,5) == selectorStart) return j;
					    }
					    return -1;
					}
	
					$(".phone-share").on( "click", function() {
						var smsKey = $(this).data("key");
						
						switch(status) {
						  case "closed":
						  	$(this)
						  		.addClass("opened");
						  	$(".phone-box").focus();
						  	status = "opened";
						      break;
						  case "opened":
						  	
						  	$(this)
						    	.removeClass("opened");
						    	status = "closed";
						  	break;
						  case "send":
						  	
						  	status = "sending";
						  	
						  	var request = { 
					  			"mode": "send",
					  			"smsKey": smsKey, 
					  			"toNumber": $(this).find(".phone-box").val().replace(/\D/g,'')
						  	};
						  	
						  	console.log(request);
						  	$(this)
						  		.removeClass("opened")
						  		.addClass("message-"+status);
						  	
						  	var smsRequest = $.post( "http://gutschurch.com/sms-replier", request,
						  		function(data) {
							  		 console.log( "Before: "+status );
							  		 console.log( data );
							  	})
								.done(function() {
									console.log( "Done: "+status );
									$(".phone-share").removeClass("message-"+status)
									status = "sent";
									$(".phone-share").addClass("message-"+status);
									
									console.log( "Success" );
								})
								.fail(function() {
									$(".phone-share").removeClass("message-"+status)
									status = "fail";
									$(".phone-share").addClass("message-"+status);
								})
								.always(function() {
									console.log( "Always: "+status );
									setTimeout(function(){
										$(".phone-share")
											.removeClass("valid-number message-"+status);
											status = "closed";
									}, 1000);
								});
							
							break;
						  default:
					     
						}//switch(mode)
					
						$(".phone-box")
							.mask("(999) 999-9999",{
							placeholder:"  ",
							completed:function(){
								$(".phone-share")
							  		.addClass("valid-number");
							  		status = "send";
							}
						});//.mask for .phone-box
					
					});//$(".phone-share").on( "click")
	
				}); }
			</script>
			
			<style>
				
				div.phone-share {
					display: block;
					font-size: 0.8em;
					font-size: 1rem;
					position: relative;
					width: 250px;
					height: 3em;
					border-radius: 30px;
					-webkit-user-select: none;
					-moz-user-select: none;
					-ms-user-select: none;
					user-select: none;
					background-color: #d0d0d0;
					background-image: -moz-linear-gradient(#d0d0d0, #fefbf7);
					background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#d0d0d0), to(#fefbf7));
					background-image: -webkit-linear-gradient(#d0d0d0, #fefbf7);
					background-image: -o-linear-gradient(#d0d0d0, #fefbf7);
				}
				div.phone-share:hover {
					cursor: pointer;
				}
				div.phone-share:after:hover {
					cursor: pointer;
				}
				
				input.phone-box {
					position: absolute;
					left: 3%;
					top: 15%;
					width: 75%;
					color: #ffffff;
					font-size: 1em;
					line-height: 1.75em;
					text-indent: 1em;
					margin: 0;
					padding: 0.17em 0;
					border-radius: 30px;
					background-color: #ffa120;
					background-image: -moz-linear-gradient(#ffa120, #e75800);
					background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#ffa120), to(#e75800));
					background-image: -webkit-linear-gradient(#ffa120, #e75800);
					background-image: -o-linear-gradient(#ffa120, #e75800);
					box-shadow: inset 0 3px 10px rgba(0, 0, 0, 0.5);
					border: none;
					outline: none;
				}
				input.phone-box::-webkit-input-placeholder {
					color: #ffa120;
				}
				
				input.phone-box:-moz-placeholder { /* Firefox 18- */
					color: #ffa120;
				}
				
				input.phone-box::-moz-placeholder {  /* Firefox 19+ */
					color: #ffa120;  
				}
				
				input.phone-box:-ms-input-placeholder {  
					color: #ffa120;  
				}
				div.phone-share span {
					display: block;
					position: absolute;
				}
				div.phone-share span:hover {
					cursor: pointer;
				}
				div.phone-share span.inner-button { 
					-webkit-transition: all 0.31s ease-in-out;
					-moz-transition: all 0.31s ease-in-out;
					-o-transition: all 0.31s ease-in-out;
					-ms-transition: all 0.31s ease-in-out;
					transition: all 0.31s ease-in-out;
					z-index: 1;
					width: 123px;
					height: 3.3em;
					top: -.2em;
					right: 20px;
					border-radius: 40px;
					background-color: #d7d7d7;
					background-image: -moz-linear-gradient(#d7d7d7, #a39f9e);
					background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#d7d7d7), to(#a39f9e));
					background-image: -webkit-linear-gradient(#d7d7d7, #a39f9e);
					background-image: -o-linear-gradient(#d7d7d7, #a39f9e);
					clip: rect(0.65em 40px 2.75em -10px);
					box-shadow: 0 3px 6px rgba(0, 0, 0, 0.6);
					
					-webkit-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					   -moz-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					    -ms-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					     -o-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
					        transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
					
					-webkit-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					   -moz-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					     -o-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					        transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000);
				}
				
				div.phone-share span.front-button {
					position: absolute;
					z-index: 3;
					padding: 0.45em;
					color: #777;
					text-shadow: 0 1px 0 rgba(255,255,255,.8);
					text-align: center;
					white-space: nowrap;
					overflow: hidden;
					width: 220px;
					height: 20px;
					top: 5px;
					right: 7px;
					border-radius: 30px;
					background-color: #f1f1f1;
					box-shadow: 0 7px 7px rgba(0, 0, 0, 0.5);
					border: 1px solid #bcb9b8;
					
					background-image: -moz-linear-gradient(#f1f1f1, #bcb9b8);
					background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#f1f1f1), to(#bcb9b8));
					background-image: -webkit-linear-gradient(#f1f1f1, #bcb9b8);
					background-image: -o-linear-gradient(#f1f1f1, #bcb9b8);
					
					-webkit-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					   -moz-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					    -ms-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					     -o-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
					        transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
					
					-webkit-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					   -moz-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					     -o-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					        transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000);
				}
				div.phone-share span.front-button:before {
					content: attr(data-message);
					/* content: "Share to Phone"; */
				}
				div.phone-share.opened span {
					display: block;
					position: absolute;
				}
				div.phone-share.opened span.inner-button {
					-webkit-transition: all 0.24s ease-in-out;
					-moz-transition: all 0.24s ease-in-out;
					-o-transition: all 0.24s ease-in-out;
					-ms-transition: all 0.24s ease-in-out;
					transition: all 0.24s ease-in-out;
					width: 70px;
				}
				div.phone-share.opened span.front-button {
					width: 60px;
				}
				div.phone-share.opened span.front-button:before {
					content: "Close";
				}
				div.phone-share.valid-number span.front-button:before {
					content: "Send";
				}
				div.phone-share.message-sending span.front-button:before {
					content: "Sending...";
				}
				div.phone-share.message-sent span.front-button:before {
					content: "Sent!";
				}
				div.phone-share.message-fail span.front-button:before {
					content: "Something went wrong";
					color: #ff0000;
				}
				div.phone-share.valid-number span.front-button {
					border: solid 1px #00aa00;
				}
				div.phone-share.valid-number span.front-button:hover {
					border: solid 1px #00ff00;
				}
				div.phone-share.valid-number span.front-button:active {
					background-color: #f1f1f1;
					background-image: none;
				}
			</style>
		<?php
	}
}

SMS::init();



class Countdown {
	static $add_script;

	static function init() {
		add_shortcode('countdown', array(__CLASS__, 'handle_shortcode'));

		add_action('init', array(__CLASS__, 'register_script'));
		
		add_action('wp_footer', array(__CLASS__, 'print_script'));
		
		add_action('wp_footer', array(__CLASS__, 'internal_script') );
	}

	static function handle_shortcode($atts) {
		self::$add_script = true;
		
		extract( shortcode_atts( array(
			'class' => false,
		), $atts, 'countdown' ) );
		
			
			include("static/countdown/local.php");
			
			$chop_json = file_get_contents("http://live.gutschurch.com/api/v1/events/current");
			
			if( $chop_json ) {
				
				$chop_json = json_decode( $chop_json );
				
				$countdownSecs = strtotime( $chop_json->response->item->eventStartTime ) - time();//D n/j ga
				
				//debug( $chop_json->response->item );
			}

		
		ob_start();
		?>
			<div id="liveCountdown" class="countdown" data-seconds="<?php echo $countdownSecs; ?>" >
				
			</div>
		<?php
		$content = ob_get_clean();
			
		return $content;
	}

	static function register_script() {
		
		//JS
		wp_register_script('inheritance', '//gutslibraries.s3.amazonaws.com/inheritance/1.0.0/jquery.plugin.min.js', array('jquery'), '1.0.0', true);
		wp_register_script('countdown', '//cdnjs.cloudflare.com/ajax/libs/jquery-countdown/2.0.0/jquery.countdown.min.js', array('jquery'), '2.0.0', true);
	}

	static function print_script() {
		if ( ! self::$add_script )
			return;
			
			//JS
			wp_print_scripts('inheritance');
			wp_print_scripts('countdown');
	}
	
	static function internal_script() {
		if ( ! self::$add_script )
			return;
		?>
			
			<script type="text/javascript">
				if ( undefined !== window.jQuery ) { jQuery(function ($) { 'use strict';
					jQuery.noConflict();
					
					var eventEnd = $("#liveCountdown").data("seconds");
				
					$("#liveCountdown").countdown({
						format: 'HMS',
						until: +eventEnd,
						alwaysExpire: true,
						layout: '<h2><a href="/live" >Live&nbsp;&nbsp;Online&nbsp;&nbsp;in: {hn} <small>{hl}</small> {mn} <small>{ml}</small> {snn} <small>{sl}</small></a></h2>',
						expiryText: '<h2><a href="/live" style="color: red;">Watch Now | live.gutschurch.com</a></h2>'
					});

				}); }
			</script>
			
			<style>
				
				.countdown { 
					width: 100%;
					height: auto;
					text-align: center;
					padding: 40px 0;
				}
				
				.countdown a {
					text-decoration: none;
				}
				
			</style>
		<?php
	}
}

Countdown::init();
