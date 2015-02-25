<?php
	
$hero = new stdClass();

function heroOrganism($hero) {
	
	switch ($hero->kind) {
		case "text":
			
		break;
		case 'media':
			
			ob_start(); ?>
			
			<div class="hero-slide" >
				
				<div class="hero-background">
					<img <?php echo $hero->srcType; ?>="<?php echo $hero->src; ?>" alt="<?php echo $hero->text; ?>">
				</div>
				
				<div class="hero-foreground animated fadeIn animated-3s animated-delay-1s" style="<?php //BG Color Overlay
					if( get_field('page_color') ): 
						?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
						?>background: rgba(<?php echo hex2rgb( get_field('page_color') ); ?>,0.85); <?php //rgba(0,0,0,0.8)
					endif; ?>">
				
					<div class="container">
						<div class="page-header">
							<h1><?php 
								echo parse_title( $hero->text ); 
							?></h1>
						</div>
					</div>
					
				</div>
				
			</div>
			
			<?php $hero->output = ob_get_clean();
			
		break;
		case 'post':
			
			ob_start(); ?>
			
			<div class="hero-slide" >
				
				<div class="hero-background">
					<img <?php echo $hero->srcType; ?>="<?php echo $hero->src; ?>" alt="<?php echo $hero->text; ?>">
				</div>
				
				<div class="hero-foreground animated fadeIn animated-3s animated-delay-1s" style="<?php //BG Color Overlay
					if( get_field('page_color') ): 
						?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
						?>background: rgba(<?php echo hex2rgb( get_field('page_color') ); ?>,0.85); <?php //rgba(0,0,0,0.8)
					endif; ?>">
				
					<div class="container">
						<div class="page-header">
							<h1><?php
								echo parse_title( $hero->text ); 
							?></h1>
							<div class="hero-content">
								<p class="byline author vcard"><?php echo __('by', 'roots'); ?> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a></p>
							</div>
							
						</div>
					</div>
					
				</div>
				
			</div>
			
			<?php $hero->output = ob_get_clean();
			
		break;
		case "event":
			
			ob_start(); ?>
			
			<div class="hero-slide" >
				
				<div class="hero-background">
					<img <?php echo $hero->srcType; ?>="<?php echo $hero->src; ?>" alt="<?php echo strip_tags( $hero->text ); ?>">
				</div>
				
				<div class="hero-foreground animated fadeIn animated-3s animated-delay-1s" style="<?php //BG Color Overlay
					if( get_field('page_color') ): 
						?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
						?>background: rgba(<?php echo hex2rgb( get_field('page_color') ); ?>,0.85); <?php //rgba(0,0,0,0.8)
					endif; ?>">
				
					<div class="container">
						<div class="page-header">
							<h1>
								<?php echo parse_title( $hero->text ); ?>
							</h1>
							
							<div class="hero-content">
								<?php echo get_the_content(); ?>
							</div>
						</div>
						
					</div>
					
				</div>
				
			</div>
			
			<?php $hero->output = ob_get_clean();
			

			
		break;
		case "program":
			
			ob_start(); ?>
			
			<div class="hero-slide" >
				
				<div class="hero-background">
					<img <?php echo $hero->srcType; ?>="<?php echo $hero->src; ?>" alt="<?php echo $hero->text; ?>">
				</div>
				
				<div class="hero-foreground animated fadeIn animated-3s animated-delay-1s" style="<?php //BG Color Overlay
					if( get_field('page_color') ): 
						?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
						?>background: rgba(<?php echo hex2rgb( get_field('page_color') ); ?>,0.85); <?php //rgba(0,0,0,0.8)
					endif; ?>">
				
					<div class="container">
						<div class="page-header">
							<div class="hero-logo">
								<img src="<?php echo $hero->logo; ?>"> 
							</div>
							<h1>
								<?php echo parse_title( $hero->text ); ?>
							</h1>
							
							<div class="hero-content">
								<?php the_excerpt(); ?>
							</div>
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
				
				<?php echo do_shortcode( $hero->text ); ?>
				
			</div>
			
			<?php $hero->output = ob_get_clean();
			

			
		break;
		default:
	}
	
	
	//Link Wrapper
	if( $hero->link && $hero->link !== "" ){ 
		
		$hero->output = '<a href="'.$hero->link.'" >'.$hero->output.'</a>';
		
	}
	
	
	//Organism Wrapper
	$hero->output = '<div class="hero-organism">'.$hero->output.'</div>';
	
	
	return $hero->output;
}



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
			
			$video->id = $_GET['vid'];
			
			$videos = new stdClass();
			
			$videos = boxVimeo( $video );
			
			$video = (object) array_merge((array) $video, (array) $videos->video_0);
			
			//debug( $video );
			
			$parameters->autoplay = 1;
		}
		
		$guts_id = "955350";
		
		if( !isset($_GET['vid']) || $video->user_id != $guts_id) {
			$video = getLatestVideo();
		}
		
		$video->query = http_build_query($parameters);
		
		ob_start(); ?>
			
			<div class="hero-foreground animated fadeIn animated-3s animated-delay-1s" style="<?php //BG Color Overlay
				if( get_field('page_color') ): 
					?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
					?>background: rgba(<?php echo hex2rgb( get_field('page_color') ); ?>,0.85); <?php //rgba(0,0,0,0.8)
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
					<iframe id="frame" src="//player.vimeo.com/video/<?php echo $video->id; ?>?<?php echo $video->query; ?>" data-gc-id="<?php echo $guts_id; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen ></iframe>
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
				    var $foreground = $(".hero-shortcode .hero-foreground");
				    var $menu = $("header.banner");
				    var lastTimeMouseMoved = "";
				    var mouseTimeout = "";
				    var vimeoPlaying = 0;
				    
				    
				    $("html").addClass("vimeo-paused")
				
				    // When the player is ready, add listeners for pause, finish, and playProgress
				    player.addEvent('ready', function() {
				        console.log('vimeo api ready');
				        
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
				    
				    $("html").addClass("watch-page");
					
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
					
					window.uid = "<?php echo $guts_id; ?>";
				
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
						
				    	$("#frame").attr('src', 'http://player.vimeo.com/video/' + id + '?byline=0&portrait=0&badge=0&color=d8d8d8&autoplay=1' );
						
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
				
				.hero-shortcode .hero-foreground {
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
	}
 
	static function handle_shortcode($atts) {
		self::$add_script = true;
		
		extract( shortcode_atts( array(
			'class' => false,
			'pages' => false,
			'logos' => true
		), $atts, 'bars' ) );
		
		global $hero;
		
		//$hero->section_class = "row";	
		
		//$bars =
		
		if( empty($pages) ){
			$pages = array( 91 , 89 ,95 ,97 ,101 );//Youth Pages
		} else {
			$pages = explode(',',$pages);
		}
		
		// WP_Query arguments
		$args = array (
			'post_type'				=> 'page',
			'post__in'				=> $pages,
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
					            	
					            	$count = $query->post_count;
					            	
					            	$options = custom_options();
					            	
					            	$post_id = get_the_ID();
					            	
					            	$featured = getImage( $post_id, '720');
					            	
					            	$logo = $featured;
					            	
					            	if( !empty( $options->logo ) ) $logo = $options->logo;
					            	
				            	?>
				            	
				            	<!-- Filler Bar -->
				            	<?php if( $p == 0 ): ?>
				            	
						            <div class="filler bar-bar col-md-1 hidden-sm hidden-xs ease" rel="" style="<?php //BG Color Overlay
										if( get_field('page_color') ): 
											?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
										endif; 
									?>"></div>
						            
					            <?php endif; ?>
								
								
								
					            <!-- bar-bar - <?php the_title(); ?> -->
					            <div class="youth-program bar-bar col-md-2 ease bar-<?php echo $p; ?>" rel="" style="<?php //BG Color Overlay
									if( get_field('page_color') ): 
										?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
									endif; ?>">
									
									<?php /* if( !$logos ){ ?>
										<div class="bar-bg-image ease-opacity">
											<img src="<?php echo $featured; ?>" alt="<?php the_title(); ?>">
										</div>
									<?php } */ ?>
									
									<div class="bar-content">
										
										<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
										
							                <div class="center">
							                	
							                    <?php if( $logos ){ ?>
							                    	<span class="bar-logo"><img src="<?php echo $logo; ?>"></span>
							                    <?php } ?>
							                    <h2><?php the_title(); ?></h2>
							                    <div class="hover-info">
								                    <p></p>
							                    </div>
							                	
												<div class="social-icons ease-height">
													<?php echo social_media_profiles(); ?>
												</div>
												
							                </div>
						                
						                </a>
						                
									</div>
									
					            </div>
				            
				            <?php $p++; endwhile; endif; wp_reset_query();//Clear query so nothing weird shows up after loop  ?>
				            
				            
				
				            
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
  
					$(".youth-program").each( function( index ){
					    
					    var $this = $(this);
					    
					    $this.hover( function(){ //mouseover
							event.stopPropagation();
							
							//Make wider
							$this
								.addClass("col-md-4 program-hovering")
								.removeClass("col-md-2");
							
							$(".youth-programs").addClass("hovering");
							
							//On mouseout
							}, function(){ 
								
								event.stopPropagation();
								
								//Make normal width
								$this
									.addClass("col-md-2")
									.removeClass("col-md-4 program-hovering");
									
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
								$(this).css("height", wh+"px");
							} else {
								$(this).css("height", "");
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

				.scrollto,
				.wrap.container {
					display: none;
				}
				
				.youth-programs {
					
				}
				
				.bar-bar {
					position: relative;
					margin: 0;
					padding: 0;
					border: none;
					height: 400px;
					text-align: center;
					overflow: hidden;
				}
								
				.bar-bar .bar-bg-image {
				    position: absolute;
				    top: 0;
				    bottom: 0;
				    left: 0;
				    right: 0;
				    
				    opacity: 0.1;
				    
				    z-index: 10;
				}
				
				.bar-bar.program-hovering .bar-bg-image {
					opacity: 0.5;
				}
				
				.bar-bar .bar-bg-image img {
					height: 100%;
					width: auto;
				}
				
				.bar-bar .bar-content {
					position: absolute;
					top: 0;
				    bottom: 0;
				    left: 0;
				    right: 0;
					
					z-index: 100;
					
				}
				
				.bar-bar .bar-content:before {
					content: '';
					display: inline-block;
					height: 100%; 
					vertical-align: middle;
					margin-right: -0.25em; /* Adjusts for spacing */
				}

				
				.bar-bar .center {
				    display: inline-block;
					vertical-align: middle;
				}
				
				.bar-bar a {
					color: #fff;
				}
				
				.bar-bar a:hover {
					text-decoration: none;
				}
				
				.bar-bar h2 {
				    text-align: center;
				    font-size: 1.25em;
				    font-weight: 700;
				    text-transform: uppercase;
				    max-width: 200px;
				}
				
				.bar-bar h2 span {
				    display: block;
				}
				
				
				.bar-bar .center img {
					max-height: 100px;
				}
				
				.social-bar-item {
					padding: 0.25em;
				}
				
				.bar-bar .hover-info {
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
				  height: 0;
				  overflow: hidden;
				}
				
				.bar-bar.program-hovering .social-icons {
					height: 50px;
				}
				
				/* section-450 */
				
				
				/* section-500 */
				
				
				
				/* section-500 */
								
			</style>
		<?php
	}
}
 
Bars::init();




/*
class Content_Hero {
	static $add_script;
	
	
 
	static function init() {
		add_shortcode('content_hero', array(__CLASS__, 'handle_shortcode'));
		
		add_action('init', array(__CLASS__, 'register_script'), 110);
		add_action('wp_footer', array(__CLASS__, 'print_script'), 110);
		add_action('wp_footer', array(__CLASS__, 'internal_script'), 110);
		
		//debug( $hero );
	}
 
	static function handle_shortcode($atts) {
		self::$add_script = true;
		
		extract( shortcode_atts( array(
			'class' => false
		), $atts, 'content_hero' ) );
		
		global $hero;
		
		$hero->logo = "";
				
		if( !empty( $hero->page_options->logo ) ) $hero->logo = $hero->page_options->logo;
				
		ob_start(); ?>
			
			
			<div class="hero-slide" >
				
				<div class="hero-background">
					<img src="<?php echo $hero->src; ?>" alt="<?php the_title(); ?>">
				</div>
				
				<div class="hero-foreground animated fadeIn animated-3s animated-delay-1s" style="<?php //BG Color Overlay
					if( get_field('page_color') ): 
						?>background: <?php echo get_field('page_color'); ?>; <?php //#000000
						?>background: rgba(<?php echo hex2rgb( get_field('page_color') ); ?>,0.85); <?php //rgba(0,0,0,0.8)
					endif; ?>">
				
					<div class="container">
						<div class="page-header">
							<div class="hero-logo">
								<img src="<?php echo $hero->logo; ?>"> 
							</div>
							<h1>
								<?php the_title(); ?>
							</h1>
							
							<div class="hero-content">
								<?php the_excerpt(); ?>
							</div>
						</div>
						
					</div>
					
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
					
					
					
				}); }
			</script>
			
			<style>
				
				
				
			</style>
		<?php
	}
}
 
Content_Hero::init();
*/


function hero() {
	
	
	global $hero;
	
	global $post;
	
	if( !isset( $hero ) ) $hero = new stdClass();
	
	$hero->classes = array();
	
	$hero->kind = "text";
	
	$hero->template = basename( get_post_meta( get_the_ID(), '_wp_page_template', TRUE ), '.php');
	
	$hero->text = get_the_title();
	
	$hero->srcType = "src";
	
	$hero->src = getImage();
	
	$hero->heroes = false;
	
	$hero->heroesCount = 0;
	
	$hero->page_options = custom_options();
	
	$hero->post = $post;
	
	if( !empty( $hero->page_options->logo ) ) $hero->logo = $hero->page_options->logo;
	
	if( get_field('heroes') ) {
		$hero->heroes = new stdClass();
		$hero->heroes = get_field('heroes');
	}
	
	array_push( $hero->classes, get_post_type() );
	
	//debug( $hero->post );
	
	if( has_post_thumbnail() || $hero->heroes ){
		
		$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'hd720' );
		
		$hero->kind = "media";
		
		$hero->media = "image";
		
		$hero->src = $thumbnail_src[0];
		
		
		if( isset( $hero->heroes[0]['image'] ) ){
			$hero->attachment_id = $hero->heroes[0]['image'];
			$image_attachment = wp_get_attachment_image_src($hero->attachment_id, 'hd720');
			$hero->src = $image_attachment[0];
		}
		
		
		//Set Kind
		
		//If Event
		if( $hero->post->post_type === 'ai1ec_event' ){
			$hero->kind = "event";
			array_push( $hero->classes, "media" );
		}
		
		//If Program
		if( is_page_template( 'template-program.php' ) ){
			$hero->kind = 'program';
			array_push( $hero->classes, "media" );
		}
		
		if( $hero->post->post_type === 'post' ){
			//$hero->kind = "post";
			array_push( $hero->classes, "media" );
			
			if( $hero->src == '' ){
				$hero->src = getImage($hero->post->id, 'hd720' );
			}
		}
		
		//If Shortcode
		if( $hero->heroes ){// if there are any heroes
			
			$hero->heroesCount = count( $hero->heroes );
			
			$hero->shortcode = parse_shortcode( trim( $hero->heroes[0]['title'] ) );
			
			if( $hero->shortcode ){// if it's a shortcode
			
				$hero->kind = "shortcode";
				
				if( isset($hero->shortcode->class) ){
					foreach( explode(' ', $hero->shortcode->class) as $name )
						array_push( $hero->classes, $name );
				}
				
			}
			
		}
			
	}
	
	//If Post
/*
	if(  ){
		$hero->kind = "post";
		array_push( $hero->classes, "media" );
	}
*/
	
	//Add kind to classes
	array_push( $hero->classes, $hero->kind );
	
	//Add Slick class if there's more than one hero
	if( $hero->heroesCount > 1 && $hero->kind !== "shortcode" ) array_push( $hero->classes, "slick" );

	
	
	ob_start();
?>
<div id="hero" class="hero-container <?php foreach ($hero->classes as &$class) if($class !== "") echo "hero-".$class." ";//echo all classes ?>container-fluid nopadding dark" data-hero-count="<?php echo $hero->heroesCount; ?>" >
	
<!--
	
	Hero Priorities
	
	Text < Image < Video < Organism
		
-->
	
	<div class="hero-section row">
		
	<?php 
		if( in_array( "text", $hero->classes ) ){
			
			//ob_start(); ?>
				
				<div class="container">
					<div class="page-header">
						<h1><?php echo $hero->text; ?></h1>
					</div>
				</div>
				
			<?php //$hero->output = ob_get_clean();
			
		}
		
		
		$hero->index = 0;
		
		//For Multiple Heroes
		if(get_field('heroes')): while(has_sub_field('heroes')): ?>
		
			<?php
				
				//Hide After
				if( get_sub_field('hide_after') && ( get_sub_field('hide_after') / 1000 + (24*60*60) ) < date('U') )//if date is set and it has passed
					continue;//Skip
				
				if( $hero->index ) $hero->srcType = 'src="'.get_template_directory_uri().'/assets/img/blank.gif" data-lazy';//if it's anything but the first lazy load it
				
				//Setup Image
				$hero->attachment_id = get_sub_field('image');
				
				$image_attachment = wp_get_attachment_image_src($hero->attachment_id, 'hd720');
				
				$hero->src = $image_attachment[0];
				
				$hero->link = get_sub_field('link');
				
				//Setup Foreground
				if( $hero->heroesCount === 1 ) {
					$hero->text = $hero->title;
					if( get_sub_field('title') ) $hero->text = get_sub_field('title');
					$hero->text = trim( $hero->text );
				} else {
					$hero->text = '';
				}
				
				$hero->heroes[$hero->index]['shortcode'] = parse_shortcode( $hero->text );
				
				if($hero->heroes[$hero->index]['shortcode']) {
				    $hero->heroes[$hero->index]['kind'] = 'shortcode';
				}
				
			?>
			
			
			<?php 
				echo heroOrganism($hero); 
				$hero->index++;
			?>
			
		
		<?php endwhile; 
		
		else: echo heroOrganism($hero); endif; ?>
		
	</div>
	
	
	<?php if( $hero->kind !== 'text'): ?>
		
		<a href="#content" class="scrollto">
			<div class="read-more hidden-sm hidden-xs">
				<span class="read-more-text">Learn More</span>
				<br>
				<span><i class="down-arrow"></i></span>
			</div>
		</a>
		
	<?php endif; ?>
	
	
	
</div>


<?php $content = ob_get_clean();
	
	return $content;
}
