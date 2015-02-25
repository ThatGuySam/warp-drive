<?php
	
function date_sort_objects($a, $b) {
	return strcmp($a->date_unix, $b->date_unix); //only doing string comparison
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
	
	$result = fetchData('https://api.instagram.com/v1/users/'.$user_id.'/media/recent/?access_token=10392439.44b554e.ab889407055b456381897320866ab6b7');
	$result = json_decode($result);
	
	$i=0;
	foreach ($result->data as $post) {
		
		$boxes[$i]['type'] =	$post->type;
		$boxes[$i]['id'] =		$post->id;
		$boxes[$i]['image_url'] = $post->images->low_resolution->url;
		//$boxes[$i]['date'] = date( "D n/j" , strtotime( $post->created_time ) );
		$boxes[$i]['text'] = 	$post->caption->text;
		$boxes[$i]['link'] = 	$post->link;
		
		$i++;
	}
	
	return $boxes;
}


function boxesVimeo($boxes) {
	
	global $hero;
	
	global $post;

	$videos =
	$output =
		new stdClass();
	
	//$vid_url = parse_url($boxes->source);
	
	$json_url = 'http://vimeo.com/api/v2'.$boxes->source.'/videos.json';
	
	$json = json_decode( @file_get_contents($json_url) );
	
	$i=0;
	foreach ($json as $video) {
		
		$details = new stdClass();
		
		$desc = explode('<br />', $video->description);
		
		//$details->strdate = strtotime( $desc[0] );
		
		$d = 0;
		foreach( $desc as $val ) {
			
			//if is not first line and contains dash
			if( $d && strpos($val,'|') !== false ) {
				
				$vals = explode('|', $val);
				
				$dkey = strtolower( trim( $vals[0] ) );
				
				$dval = trim( $vals[1] );
				
				if( empty( $dkey ) || empty( $dval ) ) continue;
				
				$details->{$dkey} = $dval;
			}
			
			$d++;
		}
		
		if( !empty($details->date) ){
			$date = strtotime( $details->date );
		} else {
			$date = strtotime( $video->upload_date );
		}
		
		$title = $video->title;
		
		$link = $boxes->site_url.'/watch/?vid='.$video->id;
		
		
		$v_box = new stdClass();
		
		$v_box->type 		= 'video';
		$v_box->id			= $video->id;
		$v_box->image_url	= $video->thumbnail_large;
		$v_box->date 		= date( 'F jS' , $date );
		$v_box->date_unix	= $date;
		$v_box->title		= $title;
		$v_box->text 		= $title;
		$v_box->desc 		= $desc[0];
		$v_box->link 		= $link;
		$v_box->index		= $i;
		
		
		$videos->{'video_'.$i} = $v_box;
		
		//if( $i >= $boxes->amount - 1 ) break;
		
		$i++;
	}
	
	$output = $videos;
	
	return $output;
}


function boxVimeo($object) {
	
	global $hero;
	
	global $post;

	$videos = $output = new stdClass();
	
	$json_url = 'http://vimeo.com/api/v2/video/'.$object->id.'.json';
	
	$video = json_decode( @file_get_contents($json_url) );
	
	$v_box = new stdClass();

	$videos->video_0 = $video;
	
	$output = $videos;
	
	return $output;
}


function getLatestVideo($album_id='2238693') {
	
	$output =
	$boxes = 
		new stdClass();
		
	$boxes->cache = new stdClass();
	
	$boxes->source = '/album/'.$album_id;
	
	$boxes->amount = 1;
	
	$boxes->id = preg_replace('/[^\da-z]/i', '', $boxes->source );
	
	$boxes->cache->function_name = 'boxesVimeo';
	$boxes->cache->cache_name = 'firstVideo';
    
    $boxes->objects = cacheHandler( $boxes );
    
    $output = $boxes->objects->video_0;
	
	return $output;
}


function boxesEvents($boxes) {

	global $ai1ec_registry;
	$date_system = $ai1ec_registry->get( 'date.system' );
	$search = $ai1ec_registry->get('model.search');

	// gets localized time
	$local_date = $ai1ec_registry->get( 'date.time', $date_system->current_time(), 'sys.default' );

	//sets start time to today
	$start_time = clone $local_date;
	$start_time->set_time( 0, 0, 0 );
	
	//sets end time to a year from today 
	$end_time = clone $start_time;
	$end_time->adjust_month( 12 );
	
	//$categories = get_the_terms($post->ID, 'ai1ec_event');
	
	$source = array( 5 );
	
	if( $boxes->source !== '' && is_numeric( $boxes->source ) ){
		$source = array( $boxes->source );
	}
	
	$filters = array(
		'cat_ids'  => $source,
		//'tag_ids'  => array(),
		//'post_ids' => array(),
		//'auth_ids' => array(),
	);
		
	$events_result = $search->get_events_between($start_time, $end_time, $filters, true);
	
	if(!empty($events_result)) {
		$e = '0';
		$e_names = array();
		foreach($events_result as $event) { 
			
			if($e >= $boxes->amount) break;//end loop
				
			//Title
			$title = $event->get( 'post' )->post_title;
			if( in_array( $title, $e_names ) ) continue;//If this has already been listed skip
			array_push($e_names,$title);//Add Name to list
			
			//Let's Go
			$e_box = new stdClass();
			
			$id = $event->get( 'post_id' );
			
			//Date
			$date_raw = $ai1ec_registry->get('view.event.time')->get_long_date( $event->get( 'start' ) );
			
			$date = date( 'F jS' , strtotime( $date_raw ) );
			
			$e_box->date				= $date;
			$e_box->date_unix			= strtotime($date_raw);
			$e_box->id					= $id;
			$e_box->title				= $title;
			$e_box->image_url			= getImage( $id );
			$e_box->type				= 'event';
			$e_box->color				= get_field('page_color', $id);
			$e_box->link				= $boxes->site_url.'/?p='.$id;
			
			$boxes->objects[$e] = $e_box;
			
			$e++;
		}
	}
	
	//Sort by date
	
	usort( $boxes->objects, 'date_sort_objects');
	
	$output = $boxes->objects;
	
	return $output;
	
}


function boxesCategory($boxes) {
	
	//debug( $boxes );
	
	// WP_Query arguments
	$args = array (
		'category_name'          => $boxes->source,
		'posts_per_page'         => '100',
	);
	
	// The Query
	$query = new WP_Query( $args );
	
	$ids = array();
	
	if( $query->have_posts() ) {
		$c = '0';
		foreach($query->posts as $post) {
			
			//Let's Go
			$c_box = new stdClass();
			
			$id = $post->ID;
			
			//Date
			$date = date( 'F jS' , strtotime( $post->post_date ) );
			
			$thumb = getImage($id);
			
			$c_box->date				= $date;
			$c_box->date_unix			= strtotime( $post->post_date );
			$c_box->id					= $id;
			$c_box->title				= $post->post_title;
			$c_box->image_url			= $thumb;
			$c_box->type				= 'post';
			$c_box->color				= get_field('page_color', $id);
			$c_box->link				= $boxes->site_url.'/?p='.$id;
			$c_box->index				= $c;
			
			$boxes->objects[$c] = $c_box;
			
			$c++;
		}
	}
	
	
	
	//Sort by date
	
	//usort( $boxes->objects, 'date_sort_objects');
	
	$output = $boxes->objects;
	
	//return $ids;
	
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
			'header'	=> false,
			'type'		=> false,
			'source'	=> false,
			'class'		=> false,
			'amount'	=> 8,
			'show'		=> 3,
			'layout'	=> 'slick'
		), $atts, 'boxes' ) );
		
		global $post;
		
		$boxes = new stdClass();
			
		$boxes->cache = new stdClass();
		
		$boxes->objects = false;
		
		$boxes->site_url = site_url();
		
		$boxes->links_type = 'normal';
		
		$boxes->post = $post;
		
		foreach ($atts as $key => $value) {$boxes->{$key} = $value;}//Convert Shortcode attributes to object values
		
		if( empty( $boxes->layout ) ) $boxes->layout = 'slick';
		
		$parsed_classes = explode(' ', $class);
		
		$boxes->child_classes = '';
		
		if( $boxes->layout === 'masonry' ){ 
			
			$col_width = 6;
			
			if( $boxes->show < 5 ) $col_width = 12 / $boxes->show;
			
			array_push( $parsed_classes , 'thin-padding');
			$boxes->child_classes = 'col-sm-6 col-md-'.$col_width;
			$boxes->srcType = 'src';
			$boxes->amount = $boxes->show;
		}
		
		$boxes->class = implode ( ' ',$parsed_classes );
		
		$props = array();
		
		foreach( $parsed_classes as $prop ) {
			$props[$prop] = true;
		}
		
		$boxes->props = $props;
		
		//$debug = $boxes->props;
		
		
		//Feed Type
		switch ($type) {
		    case 'instagram':
		        $boxes->objects = boxesInstagram($boxes);
		        
		        $target = '_blank';
		        
		        break;
		    case 'vimeo':
		        
		        $vid_url = parse_url($boxes->source);
		        
		        $boxes->source = $vid_url['path'];
		        
		        $boxes->id = preg_replace('/[^\da-z]/i', '', $vid_url['path'] );
		        
		        $boxes->cache->function_name = 'boxesVimeo';
		        $boxes->cache->cache_name = 'vimeo';
		        //$boxes->cache->json = true;
		        
		        $boxes->objects = cacheHandler( $boxes );
		        
		        if( $boxes->post->post_name === 'watch' ) {
					$boxes->links_type = 'hash';
				}
		        
		        $target = '_self';
		        
		        break;
		    case 'events':
		    	
		    	$boxes->id = $source;
		    	
		    	$boxes->cache->function_name = 'boxesEvents';
		        $boxes->cache->cache_name = 'events';
				
				$boxes->objects = cacheHandler( $boxes );
				
		        break;
		    case 'category':
		        
		        $boxes->id = $source;
		    	
		    	$boxes->cache->function_name = 'boxesCategory';
		        $boxes->cache->cache_name = 'category';
				
				$boxes->objects = cacheHandler( $boxes );
				
		        break;
		    case 'youtube':
		        echo $source;
		        break;
		    case 'acf':
		        echo $source;
		        break;
		    default:
		       echo 'Nothing here';
		}		
		
		
		ob_start();
		?>
		
		<?php 
			
			if( isset( $props['double-stacked'] ) ){
				$boxes->show = $boxes->show * 2;
			}
			
			$i=0;
		?>
			
			<div id="boxes-<?php echo $type; ?>-<?php echo $boxes->id; ?>" class="box-boxes boxes-<?php echo $type; ?> <?php echo 'boxes-'.$boxes->layout;?> <?php echo $boxes->class; ?>">
			
			<?php if( !empty( $boxes->header ) ): ?>
				<div class="boxes-header">
					<h3 class="spaced"><?php echo $boxes->header; ?></h3>
					<hr>
				</div>
			<?php endif; ?>

			<div class="frame container-fluid" data-show="<?php echo $boxes->show; ?>">
				<ul class="easecubic row">
					<?php if( $boxes->objects ): foreach($boxes->objects as $key => $box): ?>
					
						<?php 
							
							//Reset $boxes->child_class to prevent stacking
							$boxes->child_class = $boxes->child_classes;
							
							
							if( isset( $props['date-format-human'] ) ){
								$normal_date = $box->date_unix;
								
								if( strtotime( $box->title ) ) $normal_date = strtotime( $box->title );
								
								$box->title = ago( $normal_date );
							}
							
							//Hash Links
							if( $boxes->links_type === 'hash' ){
								$box->link = '#'.$box->id;
							}
							
							if( empty($boxes->srcType) ){
								$box->srcType = 'src="'.get_template_directory_uri().'/assets/img/blank.gif" data-lazy';
							} else {
								$box->srcType = $boxes->srcType;
							}
							//if( $key < $boxes->show ) $box->srcType = "src";//if is showing don't lazyload
							
							
							if( $boxes->layout === 'masonry' && $box->index >= $boxes->amount ){
								$boxes->child_class = $boxes->child_class.' box-lazyload ease-opacity';
								$box->srcType = 'src="'.get_template_directory_uri().'/assets/img/blank.gif" data-src';
							}
						?>
						
						
							<?php if( !isset( $props['double-stacked'] ) || $box->index % 2 != 0 || !$box->index ){ ?><li class="<?php echo $boxes->child_class; ?>" ><?php } ?>
								
								<div id="box-<?php echo $type; ?>-<?php echo $box->id; ?>" class="box-box box-<?php echo $box->type; ?> easecubic" style="<?php 
								//BG Color Overlay
								if( isset( $box->color ) ): 
									?>background: <?php echo $box->color; ?>; <?php //#000000
								endif; ?>">
								
									<a href="<?php echo $box->link; ?>" target="<?php echo $target; ?>" >
										<div class="box-image">
											<img class="easecubic" <?php echo $box->srcType; ?>="<?php echo $box->image_url; ?>" alt="<?php echo $box->title.' - '.$box->date; ?>" >
										</div>
										
										<div class="box-header easecubic" style="">
											<div class="box-header-content">
												<h3><?php echo parse_title( $box->title ); ?></h3>
												<div class="box-date easecubic"><?php echo $box->date; ?></div>
											</div>
										</div>
										
										<div class="box-caption easecubic"><p><?php echo parse_title( $box->desc ); ?></p></div>
										
									</a>
								</div>
								
							<?php if( !isset( $props['double-stacked'] ) || $box->index % 2 === 0 || !$box->index  ){ ?></li><?php } ?>
						
					<?php endforeach;/* foreach($boxes->objects) */ endif;/* if( $boxes->objects ) */ ?>
				</ul>
			</div>
			
		</div>
		<?php
		$content = ob_get_clean();
		
		//debug( $debug );
			
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



class Box {
	static $add_script;

	static function init() {
		add_shortcode('box', array(__CLASS__, 'handle_shortcode'));
	}

	static function handle_shortcode($atts, $content = null) {
		self::$add_script = true;
		
		extract( shortcode_atts( array(
			'title'		=> false,
			'caption'	=> false,
			'color'		=> false,
			'image'		=> false,
			'icon'		=> false,
			'type'		=> false,
		), $atts, 'box' ) );
		
		
		//
		
		$box_styles = '';
		
		if( !empty($color) ){
			$color = trim($color, '#');
			
			$alt_color = getContrastingColor($color);
			
			$box_styles .= 'background: #'.$color.'; ';
			
			$box_styles .= 'color: #'.$alt_color.'; ';
		}
		
		ob_start(); ?>
			
			<div class='box-single'>
		      <div class='ir'>
		        <div class='box-<?php echo $type; ?>' style='<?php echo $box_styles; ?>'>
			      
		          <div class='box-icon'>
		            <i class='<?php echo $icon; ?>'></i>
		          </div>
		          
		          <div class='box-image'>
				  	<?php echo $content; ?>
		          </div>
		          
		          <div class='box-header'>
		            <h3 class='box-header-content'><?php echo $title; ?></h3>
		            <div class='box-header-sub'>
		              <span><?php echo $caption; ?></span>
		            </div>
		          </div>
		          
		        </div>
		      </div>
		    </div>
			
		<?php $content = ob_get_clean();
			
		return $content;
	}

}

Box::init();