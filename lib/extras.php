<?php

require get_theme_root().'/'.get_template().'/lib/vendor/autoload.php';

use League\ColorExtractor\Client as ColorExtractor;

$client = new ColorExtractor;

include get_theme_root().'/'.get_template().'/inc/snippets.php';

include get_theme_root().'/'.get_template().'/inc/filters.php';

include get_theme_root().'/'.get_template().'/inc/boxes.php';

//include get_theme_root().'/'.get_template().'/inc/sms.php';


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
}


/*

Menu

*/

register_nav_menus(array(
	'expanded_navigation' => __('Expanded Navigation')
));


function heroOrganism($hero) {
	
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
			
				<img <?php echo $hero->srcType; ?>="<?php echo $hero->src; ?>" alt="">
				
				<div class="foreground fadeIn fadeIn-3s fadeIn-Delay-2s" style="<?php //BG Color Overlay
					if( get_field('page_color') ): 
						?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
						?>background: rgba(<?php echo hex2rgb( get_field('page_color') ); ?>,0.8); <?php //rgba(0,0,0,0.8)
					endif; ?>">
				
					<div class="container">
						<div class="page-header">
							<h1>
								<?php echo parse_title( $hero->text ); ?>
							</h1>
						</div>
						
						<div class="hero-content">
							<?php //echo do_shortcode('[content]'); ?>
						</div>
						
					</div>
					
				</div>
				
			</div>
			
			<?php $hero->output = ob_get_clean();
			

			
		break;
		case "video":
			//Video BG
		break;
		case "shortcode":
		
			ob_start(); ?>
			
			<div class="hero-slide" >
			
				<img <?php echo $hero->srcType; ?>="<?php echo $hero->src; ?>" alt="">
				
				<div class="foreground" style="<?php //BG Color Overlay
					if( get_field('page_color') ): 
						?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
						?>background: rgba(<?php echo hex2rgb( get_field('page_color') ); ?>,0.8); <?php //rgba(0,0,0,0.8)
					endif; ?>">
				
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
			

			
		break;
		default:
	}
	
	
	//Link Wrapper
	if( $hero->link && $hero->link !== "" ): ob_start(); ?>
		
		<a href="<?php echo $hero->link; ?>" >
		
			<?php echo $hero->output; ?>
			
		</a>
		
	<?php $hero->output = ob_get_clean(); endif;
	
	
	//Organism Wrapper
	ob_start(); ?>
		<div class="hero-organism">
			<?php echo $hero->output; ?>
		</div>
	<?php $hero->output = ob_get_clean();
	
	
	return $hero->output;
}





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
