<?php

date_default_timezone_set('America/Chicago');

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

register_nav_menus(array(
	'footer_navigation' => __('Footer Navigation')
));


$hero = new stdClass();

function heroOrganism($hero) {
	
	switch ($hero->kind) {
		case "text":
			
			ob_start(); ?>
				
				<div class="container">
					<div class="page-header">
						<h1>
							<?php echo $hero->text; ?>
						</h1>
					</div>
				</div>
				
			<?php $hero->output = ob_get_clean();
			
		break;
		case "media":
		
			ob_start(); ?>
			
			<div class="hero-slide" >
				
				<div class="hero-background">
					<img <?php echo $hero->srcType; ?>="<?php echo $hero->src; ?>" alt="">
				</div>
				
				<div class="foreground animated fadeIn animated-3s animated-delay-1s" style="<?php //BG Color Overlay
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
				
				<?php //debug( $hero ); ?>
			
				<?php echo do_shortcode( $hero->text ); ?>
				
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


/*

Static Organisms

*/

function socialBar() {

ob_start(); ?>

<div class="social-container container-fluid dark">
	
	<div class="social-bar row">
		
		<div class="social-bar-item col-sm-1 col-sm-offset-4 col-xs-2 col-xs-offset-2">
			<a href="https://www.facebook.com/gutschurch">
				<i class="fa fa-facebook fa-2x"></i>
			</a>
		</div>
		<div class="social-bar-item col-sm-1 col-xs-2">
			<a href="https://twitter.com/gutschurch">
				<i class="fa fa-twitter fa-2x"></i>
			</a>
		</div>
		<div class="social-bar-item col-sm-1 col-xs-2">
			<a href="http://instagram.com/gutschurch">
				<i class="fa fa-instagram fa-2x"></i>
			</a>
		</div>
		<div class="social-bar-item col-sm-1 col-xs-2">
			<a href="http://www.pinterest.com/gutschurch/">
				<i class="fa fa-pinterest fa-2x"></i>
			</a>
		</div>
				
	</div>
	
</div>

<?php $content = ob_get_clean();
	
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




class Watch {
	static $add_script;
 
	static function init() {
		add_shortcode('watch', array(__CLASS__, 'handle_shortcode'));
		
		add_action('init', array(__CLASS__, 'register_script'), 110);
		add_action('wp_footer', array(__CLASS__, 'print_script'), 110);
		add_action('wp_footer', array(__CLASS__, 'internal_script'), 110);
	}
 
	static function handle_shortcode($atts) {
		self::$add_script = true;
		
		extract( shortcode_atts( array(
			'class' => false,
		), $atts, 'watch' ) );
		
		
		$video = new stdClass();
		
		$parameters = new stdClass();
			$parameters->title = 0;
			$parameters->byline = 0;
			$parameters->portrait = 0;
			$parameters->color = "ffffff";
			$parameters->api = 1;
			$parameters->player_id = 'frame';
		
		
		if ( isset($_GET['vid']) ) {
			
			$decoded = json_decode( getJson('http://vimeo.com/api/v2/video/'.$_GET['vid'].'.json') );
			
			$video = $decoded[0];
			
			$parameters->autoplay = 1;
		}
		
		$video->guts_id = "955350";
		
		if( !isset($_GET['vid']) || $video->user_id != $video->guts_id ) {
			$video = getLatestVideo();
		}
		
		$video->query = http_build_query($parameters);
		
		ob_start(); ?>
			
			<div class="foreground animated fadeIn animated-3s animated-delay-1s" style="<?php //BG Color Overlay
				if( get_field('page_color') ): 
					?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
					?>background: rgba(<?php echo hex2rgb( get_field('page_color') ); ?>,0.8); <?php //rgba(0,0,0,0.8)
				endif; ?>">
			
				<div class="container">
					<div class="page-header">
					</div>
					
					<div class="hero-content">
						<?php //echo do_shortcode('[content]'); ?>
					</div>
					
				</div>
				
			</div>
			
			<div class="hero-background">
				<div class="ir frame-container">
					<iframe id="frame" src="//player.vimeo.com/video/<?php echo $video->id; ?>?<?php echo $video->query; ?>" data-gc-id="<?php echo $video->guts_id; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen ></iframe>
				</div>
			</div>
			
		<?php
		$content = ob_get_clean();
			
		return $content;
	}
 
	static function register_script() {
		//CSS
		//wp_register_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', array(), '4.2.0', 'screen' );
		
		//JS
		wp_register_script( 'jquery-migrate-cdn', '//code.jquery.com/jquery-migrate-1.2.1.min.js', array('jquery'), '1.2.1', true);
		wp_register_script( 'froogaloop', '//f.vimeocdn.com/js/froogaloop2.min.js', array('jquery'), '2.0', true);
		wp_register_script( 'hashchange', '//cdnjs.cloudflare.com/ajax/libs/jquery-hashchange/v1.3/jquery.ba-hashchange.min.js', array('jquery'), '1.3', true);
		
	}
 
	static function print_script() {
		if ( ! self::$add_script )
			return;
			
			//CSS
			//if( wp_style_is( 'js_composer_front', 'registered' ) ) wp_print_styles('js_composer_front');
			
			//JS
			wp_print_scripts('jquery-migrate-cdn');
			wp_print_scripts('froogaloop');
			wp_print_scripts('hashchange');
			
	}
	
	static function internal_script() {
		if ( ! self::$add_script )
			return;			
		?>
			
			<script type="text/javascript">
				if ( undefined !== window.jQuery ) { jQuery(function ($) { 'use strict';
						
					
					var field = 'vid';
					var vid;
					var videoTitle;
					
					
					
					// Vimeo Player API
					
					var iframe = $('#frame')[0];
				    var player = $f(iframe);
				    var status = $('.status');
				    var gcID = $('#frame').data("gc-id");
				    var $foreground = $(".hero-shortcode .foreground");
				    var $menu = $("header.banner");
				    var lastTimeMouseMoved = "";
				    var mouseTimeout = "";
				    var vimeoPlaying = 0;
				
				    // When the player is ready, add listeners for pause, finish, and playProgress
				    player.addEvent('ready', function() {
				        console.log('ready');
				        
				        player.addEvent('pause', onPause);
				        player.addEvent('play', onPlay);
				        //player.addEvent('playProgress', onPlayProgress);
				    });
				
				    function onPause(id) {
				        console.log('paused');
				        $("html").removeClass("vimeo-playing").addClass("vimeo-paused");
				        vimeoPlaying = 0;
				    }
				    
				    function onPlay(id) {
				        console.log('played');
				        $("html").removeClass("vimeo-paused").addClass("vimeo-playing");
				        vimeoPlaying = 1;
				    }
				    
				    $("html").addClass("watch-page transparent-menu");
					
					$foreground.click(function(){
						
						if( vimeoPlaying ){
							player.api("pause");
						} else {
							player.api("play");
						}
					});
					
					
					//Wake Up Video on Mouse action
					$(document).bind("mousemove onmousemove onmousedown mousedown onclick click scroll DOMMouseScroll mousewheel keyup", function(){
						
						console.log("Wake Up!");
						
						$("html").removeClass("clean-hero");
						$foreground.addClass("disable-hover");
						
						lastTimeMouseMoved = new Date().getTime();
						
						$menu.hover(function(){
							clearTimeout(mouseTimeout);
						});
						
						mouseTimeout=setTimeout(function(){
							var currentTime = new Date().getTime();
							if(currentTime - lastTimeMouseMoved > 1000){
								$("html").addClass("clean-hero");
								$foreground.removeClass("disable-hover");
							}
						},2000);
					});
					
					window.uid = "<?php echo $video->guts_id; ?>";
				
					function getParameterByName(name) {
					  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
					  var regexS = "[\\?&]" + name + "=([^&#]*)";
					  var regex = new RegExp(regexS);
					  var results = regex.exec(window.location.search);
					  if(results == null)
					    return "";
					  else
				      return decodeURIComponent(results[1].replace(/\+/g, " "));
					}
					
					function loadVideo(id) {
						
				    	$("#frame").attr('src', 'http://player.vimeo.com/video/' + id + '?byline=0&portrait=0&badge=0&color=a20000&autoplay=1' );
				    	//window.videoTitle = title;
				    	//window.vid = id;
			
			
/*
						$('#toolbox').removeClass().addClass('fadeInRight animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
							//$(this).removeClass();
						});
*/
						
						gc.scrollTo( $("#hero").offset().top );
					}
					
/*
					if( window.location.href.indexOf('?' + field + '=') != -1 && uid == "955350") {
						$(document).ready(function() {
						window.vid = getParameterByName('vid')
						var vid = window.vid;
							loadVideo(vid);
						});
					}
*/
					
					//Load Video from Hash
					if(window.location.hash !== "") {
						
						var vid = window.location.hash.split('#')[1]
						window.vid = vid;
						
						$.getJSON('http://vimeo.com/api/v2/video/'+vid+'.json', {format: 'json'}, function(data) {
						    window.uid = data[0].user_id;
						    //console.log(window.uid);
						})
						.done(function() {
							if( window.uid == gcID){
								loadVideo(vid);
							}
						});
						
					}
			
					//Load Video from Hash Changing(click)
					jQuery(window).hashchange(function () {
						var hash = window.location.hash.split('#')[1]
						loadVideo(hash);
					});

					
				}); }
			</script>
			
			<style>
				
				.hero-background {
					z-index: 0;
				}
				
				.hero-shortcode .foreground {
					position: absolute;
					z-index: 1;
				}
				
				.frame-container {
					padding-bottom: 0;
					height: 100%;
					position: absolute;
				}
				
				#frame {
				}
				
			</style>
		<?php
	}
}
 
Watch::init();




class Bars {
	static $add_script;
	
	
 
	static function init() {
		add_shortcode('bars', array(__CLASS__, 'handle_shortcode'));
		
		add_action('init', array(__CLASS__, 'register_script'), 110);
		add_action('wp_footer', array(__CLASS__, 'print_script'), 110);
		add_action('wp_footer', array(__CLASS__, 'internal_script'), 110);
		
		//debug( $hero );
	}
 
	static function handle_shortcode($atts) {
		self::$add_script = true;
		
		extract( shortcode_atts( array(
			'class' => false,
			'pages' => false
		), $atts, 'bars' ) );
		
		//global $hero;
		
		//$hero->section_class = "row";
		
		//debug( $hero );
		
		
		// WP_Query arguments
		$args = array (
			'post_type'				=> 'page',
			'post__in'				=> array( 91,89,95,97,101 ),//Youth Pages
			'orderby'				=> 'date',
		);
		
		// The Query
		$query = new WP_Query( $args );
		
		//$query->post_count
		
		$last_post = $query->posts[$query->post_count-1];
		
		$hero_color = get_field('page_color',$last_post->ID);
		
		ob_start(); ?>
			
	        <div class="container-fluid">

				<div class="row youth-programs" style="<?php if( $hero_color ) echo "background:".$hero_color.";"; ?>">
				            
				  
				            <?php $p=0; if ( $query->have_posts() ): while ( $query->have_posts() ): $query->the_post();  ?>
				            	
				            	<?php  
					            	
					            	$options = custom_options();
					            	
				            	?>
				            	
				            	<?php if( $p == 0 ): ?>
				            	
						            <div class="filler bar-bar col-md-1 hidden-sm hidden-xs ease-width" rel="" style="<?php //BG Color Overlay
									if( get_field('page_color') ): 
										?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
									endif; ?>">
						            </div>
						            
					            <?php endif; ?>
								
								
								
					            <!-- bar-bar - Safari -->
					            <div class="youth-program bar-bar col-md-2 ease-width bar-<?php echo $p; ?>" rel="" style="<?php //BG Color Overlay
									if( get_field('page_color') ): 
										?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
									endif; ?>">
										
					                <div class="center">
					                	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
						                    <span><img src="<?php echo getThumb( get_the_ID(), 'full'); ?>"></span>
						                    <h2><?php the_title(); ?></h2>
						                    <div class="hover-info">
							                    <p></p>
						                    </div>
					                	</a>
					                	
					                	
										<div class="social-icons">
											<?php if( isset( $options->facebook ) ): ?><a href="<?php echo $options->facebook; ?>"><i class="fa fa-facebook"></i></a><?php endif; ?>
											<?php if( isset( $options->twitter ) ): ?><a href="<?php echo $options->twitter; ?>"><i class="fa fa-twitter"></i></a><?php endif; ?>
											<?php if( isset( $options->instagram ) ): ?><a href="<?php echo $options->instagram; ?>"><i class="fa fa-instagram"></i></a><?php endif; ?>
										</div>
					                </div>
					            </div>
				            
				            <?php $p++; endwhile; endif; ?>
				            
				            
				
				            
				        <!-- end SECTION -->
				        </div>
				
				</div>

		<?php
		$content = ob_get_clean();
			
		return $content;
	}
 
	static function register_script() {
		//CSS
		//wp_register_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', array(), '4.2.0', 'screen' );
		
		//JS
		//wp_register_script( 'jquery-migrate-cdn', '//code.jquery.com/jquery-migrate-1.2.1.min.js', array('jquery'), '1.2.1', true);		
	}
 
	static function print_script() {
		if ( ! self::$add_script )
			return;
			
			//CSS
			//wp_print_styles('font-awesome');
			
			//JS
			//wp_print_scripts('jquery-migrate-cdn');
			
	}
	
	static function internal_script() {
		if ( ! self::$add_script )
			return;			
		?>
			
			<script type="text/javascript">
				if ( undefined !== window.jQuery ) { jQuery(function ($) { 'use strict';
						
					  //$(".youth-programs").css("height", window.innerHeight+"px" );
  
					  $(".youth-program").each( function( index ){
					    
					    var $this = $(this);
					    
					    $this.hover( function(){ //mouseover
							event.stopPropagation();
							
							$this
								.addClass("col-md-4 program-hovering")
								.removeClass("col-md-2");
							
								$(".youth-programs").addClass("hovering");
							}, 
							function(){ //mouseout
								
								event.stopPropagation();
							
								$this
									.addClass("col-md-2")
									.removeClass("col-md-4 program-hovering")
									.siblings()
									.addClass("")
									.removeClass("");
									
									$(".youth-programs").removeClass("hovering");
								}
						);//$this.hover
					    
					  });
					  
						function sizeBars() {
						
							var $media = $(".bar-bar");
							
							var wh = window.innerHeight;
							var ww = window.innerWidth;
							
							
							
								$media.each(function() {
									
									if( ww > 992 ){
										$(this)
											.css("height", wh+"px");
									} else {
										$(this)
											.css("height", "");
									}
									
								});
							
							
						}
						
						$( window ).resize(function() {
							sizeBars();
						});
						
						sizeBars();

					
				}); }
			</script>
			
			<style>
				
/*
				.hero-background {
					z-index: 0;
				}
				
				.hero-shortcode .foreground {
					position: absolute;
					z-index: 1;
				}
				
				.frame-container {
					padding-bottom: 0;
					height: 100%;
					position: absolute;
				}
				
				#frame {
				}
*/
				.scrollto,
				.wrap.container {
					display: none;
				}
				
				
				/* section-000 */


				/* section-400 */
				
				.youth-programs {
					
				}
				
				.bar-bar {
				  /*position: relative;*/
				  /*width: 24.5%;*/
				  /*display: inline-block;*/
				  margin: 0;
				  padding: 0;
				  border: none;
				  /* display: inline-block; */
				  /* height: 100%; */
				  height: 300px;
				  text-align: center;
				}
				
				.bar-bar:before {
					content: '';
					display: inline-block;
					height: 100%; 
					vertical-align: middle;
					margin-right: -0.25em; /* Adjusts for spacing */
					
					/* For visualization 
					background: #808080; width: 5px;
					*/
				}
				
				.bar-bar .center {
				    display: inline-block;
					vertical-align: middle;
				}
				
				
				.bar-bar a {
				  color: #fff;
				}
				
				.bar-bar h2 {
				    text-align: center;
				    font-size: 1.25em;
				    font-weight: 700;
				    text-transform: uppercase;
				}
				
				.bar-bar h2 span {
				    display: block;
				}
				
				
				.bar-bar .center img {
					max-width: 120px;
				}
				
				.bar-bar .hover-info {
				  /* width: 250px; */
				  padding: 1em;
				  opacity: 0;
				}
				
				
				.program-hovering .bar-bar .hover-info {
				  opacity: 1;
				}
				
				
				.hovering .filler {
				  width: 0;
				}
				
				.bar-bar .social-icons {
				  text-align: center;
				  vertical-align: bottom;
				  color: #fff;
				  font-size: 1.5em;
				  letter-spacing: 0.15em;
				}
				/* section-450 */
				
				
				/* section-500 */
				
				
				
				/* section-500 */
								
			</style>
		<?php
	}
}
 
Bars::init();