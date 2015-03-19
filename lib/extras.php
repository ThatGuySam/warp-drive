<?php

date_default_timezone_set('America/Chicago');

$GLOBALS['theme_dir'] = get_theme_root();

//$GLOBALS['root_dir'] = get_home_path();

require $GLOBALS['theme_dir']. DIRECTORY_SEPARATOR . get_template().'/lib/vendor/autoload.php';

use League\ColorExtractor\Client as ColorExtractor;

$client = new ColorExtractor;

//mobiledetect.net/
global $detect;
$detect = new Mobile_Detect;


include get_theme_root().'/'.get_template().'/inc/snippets.php';

include get_theme_root().'/'.get_template().'/inc/filters.php';

include get_theme_root().'/'.get_template().'/inc/heroes.php';

include get_theme_root().'/'.get_template().'/inc/boxes.php';


global $page_options;

if( array_key_exists( 'watchservice' , $_GET ) ) {
	wp_redirect( currentServiceLink() );
	exit;
};


/*
if ( $detect->isMobile() ) {
	echo "Mobile!";
} else {
	debug( $detect );
}
*/


//Detect First Load
/*
add_action('init', 'first_load');
function first_load() {
	
	if ( isset( $_COOKIE['firsttime'] ) ) {
		$_COOKIE['firsttime'] = false;
	} else {//it's the first load
		setcookie("firsttime", true, time() + (86400 * 30) );
	}
	
	
}
*/

add_action('wp', 'custom_options_actions');
function custom_options_actions() {
	
	global $post;
	global $detect;//Device Detection
	global $page_options;
	global $user_redirect_url;
	
	$page_options = custom_options();
	
	//If there aren't any options set
	if( empty( $page_options ) ) return false;
	
	//debug( clean_user_url( $page_options->redirect_ios ) );
		
	$user_url = false;
	
	//Redirects
	//Android
	if( !empty( $page_options->redirect_android ) && $detect->isAndroidOS() ) $user_url = $page_options->redirect_android;
	//iOS
	if( !empty( $page_options->redirect_ios ) && $detect->isiOS() ) $user_url = $page_options->redirect_ios;
	//Windows Phone
	if( !empty( $page_options->redirect_wf ) && $detect->is('Windows Phone') ) $user_url = $page_options->redirect_wf;
	//Default
	if( !empty( $page_options->redirect ) ) $user_url = $page_options->redirect;
	
	if($user_url){
		//$redirect_url = clean_user_url( $user_url );
		wp_redirect( $user_url, '301' );
		
		debug( $user_url );
		
		$user_redirect_url = $user_url;
		
		function custom_options_js_redirect() {
			global $user_redirect_url;
		    echo '<script type="text/javascript">window.location.replace("' . $user_redirect_url . '");</script>//'.$user_url;
		}
		add_action('wp_head', 'custom_options_js_redirect');
	}
	
}


/* Plugin Deregisters to avoid redendancy after script concatenation */
add_action('wp_print_styles', 'deregister_styles', 100);

function deregister_styles() {
	
	$scripts = array(
		array('js', 'wpb_composer_front_js'),
		array('css', 'js_composer_front'),// Visual Composer
		array('css', 'js_composer_custom_css'),
		array('css', 'ai1ec-general'),// All-in-one Events
		array('css', 'ai1ec-event'),
		array('css', 'ai1ec-calendar'),
		//array('js', 'dsq_count_script'),//Disqus
		//array('ai1ec_style','css')
	);
	
	foreach($scripts as $script){
		
		$slug = $script[1];
		$type = $script[0];
		
		switch ($type) {
		    case 'js':
		        if( wp_script_is( $slug, 'registered' ) )	wp_deregister_script($slug);
		        if( wp_script_is( $slug, 'enqueued' ) )		wp_dequeue_script($slug);
		        break;
		    case 'css':
		        if( wp_style_is( $slug, 'registered' ) )	wp_deregister_style($slug);
		        if( wp_style_is( $slug, 'enqueued' ) )		wp_dequeue_style($slug);
		        break;
		}	
		
			
	}
}

/*

Menu

*/

register_nav_menus(array(
	'expanded_navigation' => __('Expanded Navigation')
));

register_nav_menus(array(
	'footer_navigation' => __('Footer Navigation')
));

/*

Static Organisms

*/

function socialBar() {

$profiles = social_media_profiles(true);

if( $profiles ): ob_start(); ?>

<div class="social-container container-fluid dark <?php global $page_options; if( isset( $page_options->pagefade ) ) {?>animated fadeIn animated-3s animated-delay-1s<?php } ?>">
	
	<div class="social-bar row">
		
		<?php echo $profiles; ?>
		
	</div>
	
</div>

<?php $content = ob_get_clean(); endif;
	
	return $content;
}


/*

Living Organisms

*/


class Current_Service_Link {
	static $add_script;
 
	static function init() {
		add_shortcode('current_service', array(__CLASS__, 'handle_shortcode'));
		
		//add_action('init', array(__CLASS__, 'register_script'));
		//add_action('wp_footer', array(__CLASS__, 'print_script'));
		//add_action('wp_footer', array(__CLASS__, 'internal_script'));
	}
 
	static function handle_shortcode($atts) {
		//self::$add_script = true;
		
		extract( shortcode_atts( array(
			'class' => false,
		), $atts, 'current_service' ) );
			
		return currentServiceLink();
	}
}
Current_Service_Link::init();




class Content_Insert {
	static $add_script;
 
	static function init() {
		add_shortcode('content', array(__CLASS__, 'handle_shortcode'));
		
		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
		add_action('wp_footer', array(__CLASS__, 'internal_script'));
	}
 
	static function handle_shortcode($atts) {
		self::$add_script = true;
		
		extract( shortcode_atts( array(
			'class' => false,
			'page' => get_the_ID()
		), $atts, 'content' ) );
		
		
		ob_start();
		
		// Default output if no pageid given
		 $output = NULL;
		
		 // extract atts and assign to array
		 
		 // if a page id is specified, then run query
		 if (!empty($page)) {
			 $pageContent = new WP_query();
			 $pageContent->query(array('page_id' => $page,'post_type' => array('page', 'ai1ec_event'),));
			 while ($pageContent->have_posts()) : $pageContent->the_post();
			 	// assign the content to $output
			 	the_content();
			 endwhile;
			 wp_reset_query();
		 }
 
		?>
			
			
			
			
			
		<?php
		$content = ob_get_clean();
			
		return $content;
	}
 
	static function register_script() {
		//CSS
		wp_register_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', array(), '4.2.0', 'screen' );
		
		//JS
		wp_register_script( 'jquery-plugin', get_stylesheet_directory_uri() . '/assets/js/jquery.plugin.js', array('jquery'), '1.0', true);
		
	}
 
	static function print_script() {
		if ( ! self::$add_script )
			return;
			
			//CSS
			if( wp_style_is( 'js_composer_front', 'registered' ) ) wp_print_styles('js_composer_front');
			
			//JS
			//wp_print_scripts('jquery-plugin');
			
	}
	
	static function internal_script() {
		if ( ! self::$add_script )
			return;
			
			
						
		?>
			
			<script type="text/javascript">
				if ( undefined !== window.jQuery ) { jQuery(function ($) { 'use strict';
					
					
					
				}); }
			</script>
			
			<style>
				
			</style>
		<?php
	}
}

Content_Insert::init();


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
			
			$countdown = new stdClass();
			$countdown->cache = new stdClass();
			
			//Define countdown options
			$countdown->cache->function_name =	'countdownEvents';
			$countdown->cache->cache_time =		'-15 minutes';
			$countdown->cache->cache_name =		'countdown';
		    
		    //Get any events
		    $countdown->objects = cacheHandler( $countdown );
		    
		    
		    //Parse next event date into js friendly date
		    $until_js = date( 'Y/m/d H:i:00', $countdown->objects[0]->start_time );
		
		ob_start();
		?>
			<span class="countdown" data-until="<?php echo $until_js; ?>" ></span>
		<?php
		$content = ob_get_clean();
			
		return $content;
	}

	static function register_script() {
		
		//JS
		//wp_register_script('inheritance', '//gutslibraries.s3.amazonaws.com/inheritance/1.0.0/jquery.plugin.min.js', array('jquery'), '1.0.0', true);
		//wp_register_script('countdown', '//cdnjs.cloudflare.com/ajax/libs/jquery-countdown/2.0.0/jquery.countdown.min.js', array('jquery'), '2.0.0', true);
	}

	static function print_script() {
		if ( ! self::$add_script )
			return;
			
			//JS
			//wp_print_scripts('inheritance');
			//wp_print_scripts('countdown');
	}
	
	static function internal_script() {
		if ( ! self::$add_script )
			return;
		?>
			
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
				
				.countdown .live {
					text-shadow: 0 0 3px #ff0000;
				}
				
			</style>
		<?php
	}
}

Countdown::init();


class Email_Signup {
	static $add_script;
 
	static function init() {
		add_shortcode('email', array(__CLASS__, 'handle_shortcode'));
		
		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
		add_action('wp_footer', array(__CLASS__, 'internal_script'));
	}
 
	static function handle_shortcode($atts) {
		//self::$add_script = true;
		
		extract( shortcode_atts( array(
			'class' => false,
			'action' => 'http://submit.jotform.us/submit.php',
			'text' => 'Get Updates'
		), $atts, 'email' ) );
		
		ob_start();
		?>
			
			
			<form class="switch-input mode-closed" action="<?php echo $action; ?>" method="post" name="form_43207844374962" id="43207844374962" accept-charset="utf-8">
				<input type="hidden" name="formID" value="43207844374962" />
				<input class="switch-input-box light" type="text" id="input_1" name="q1_email1" size="30" placeholder="Email Address"/>
				<span class="front-button btn" data-message="Subscribe"></span>
			</form>

		<?php
		$content = ob_get_clean();
			
		return $content;
	}
 
	static function register_script() {
		//CSS
		//wp_register_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', array(), '4.2.0', 'screen' );
		
		//JS
		//wp_register_script( 'jquery-plugin', get_stylesheet_directory_uri() . '/assets/js/jquery.plugin.js', array('jquery'), '1.0', true);
		
	}
 
	static function print_script() {
		if ( ! self::$add_script )
			return;
			
			//CSS
			//if( wp_style_is( 'js_composer_front', 'registered' ) ) wp_print_styles('js_composer_front');
			
			//JS
			//wp_print_scripts('jquery-plugin');
			
	}
	
	static function internal_script() {
		if ( ! self::$add_script )
			return;
			
			
						
		?>
			
			<script type="text/javascript">
				if ( undefined !== window.jQuery ) { jQuery(function ($) { 'use strict';
					
					
					
				}); }
			</script>
			
			<style>
				
								
			</style>
		<?php
	}
}
 
Email_Signup::init();



class Social_Icons {
	static $add_script;
 
	static function init() {
		add_shortcode('social_icons', array(__CLASS__, 'handle_shortcode'));
		
		//add_action('init', array(__CLASS__, 'register_script'));
		//add_action('wp_footer', array(__CLASS__, 'print_script'));
		//add_action('wp_footer', array(__CLASS__, 'internal_script'));
	}
 
	static function handle_shortcode($atts) {
		//self::$add_script = true;
		
		extract( shortcode_atts( array(
			'class' => false,
			'facebook' => false,
			'twitter' => false,
			'instagram' => false,
			'pinterest' => false,
			'email' => false,
			'youtube' => false
		), $atts, 'social_icons' ) );
		
		ob_start();
		
					
		?>
			
			
		<?php
		$content = ob_get_clean();
			
		return $content;
	}
 
	static function register_script() {
		//CSS
		//wp_register_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', array(), '4.2.0', 'screen' );
		
		//JS
		//wp_register_script( 'jquery-plugin', get_stylesheet_directory_uri() . '/assets/js/jquery.plugin.js', array('jquery'), '1.0', true);
		
	}
 
	static function print_script() {
		if ( ! self::$add_script )
			return;
			
			//CSS
			//if( wp_style_is( 'js_composer_front', 'registered' ) ) wp_print_styles('js_composer_front');
			
			//JS
			//wp_print_scripts('jquery-plugin');
			
	}
	
	static function internal_script() {
		if ( ! self::$add_script )
			return;
			
			
						
		?>
			
			<script type="text/javascript">
				if ( undefined !== window.jQuery ) { jQuery(function ($) { 'use strict';
					
					
					
				}); }
			</script>
			
			<style>
				
								
			</style>
		<?php
	}
}
 
Social_Icons::init();