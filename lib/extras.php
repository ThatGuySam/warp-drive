<?php

date_default_timezone_set('America/Chicago');


require get_theme_root().'/'.get_template().'/lib/vendor/autoload.php';


use League\ColorExtractor\Client as ColorExtractor;

$client = new ColorExtractor;

$detect = new Mobile_Detect;


include get_theme_root().'/'.get_template().'/inc/snippets.php';

include get_theme_root().'/'.get_template().'/inc/filters.php';

include get_theme_root().'/'.get_template().'/inc/heroes.php';

include get_theme_root().'/'.get_template().'/inc/boxes.php';

//include get_theme_root().'/'.get_template().'/inc/sms.php';


/*
if ( $detect->isMobile() ) {
	echo "Mobile!";
} else {
	debug( $detect );
}
*/


add_image_size( '1080', 1920, 1080, true );
add_image_size( '720', 1280, 720, true );
add_image_size( '360', 640, 360, true );
add_image_size( 'thumb-hd', 400, 225, true );


/* Plugin Deregisters to avoid redendancy after script concatenation */
add_action('wp_print_styles', 'deregister_styles', 100);

function deregister_styles() {
	//Visual Composer
	if( wp_style_is( 'js_composer_front', 'registered' ) )		wp_deregister_style('js_composer_front');
	if( wp_style_is( 'js_composer_custom_css', 'registered' ) )	wp_deregister_style('js_composer_custom_css');
	if( wp_script_is( 'wpb_composer_front_js', 'registered' ) )	wp_deregister_script('wpb_composer_front_js');
	
	
	wp_deregister_script('ai1ec_requirejs');
	
	wp_deregister_style('ai1ec-general');
	wp_deregister_style('ai1ec-event');
	wp_deregister_style('ai1ec-calendar');
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

<div class="social-container container-fluid dark">
	
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
			$countdown->cache->function_name =	"countdownEvents";
			$countdown->cache->cache_time =		'-15 minutes';
			$countdown->cache->cache_name =		"countdown";
		    
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