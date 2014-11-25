<?php

function cacheHandler( $object, $function ) {
    // cache files are created like cache/abcdef123456...
    
    $filename = $function . "-" . preg_replace("/[^\da-z]/i", '', $object->id );
    $cacheFile = 'cache' . DIRECTORY_SEPARATOR . $filename;
	
	if( array_key_exists( 'purge' , $_GET ) ) unlink($cacheFile);
	
    if (file_exists($cacheFile)) {
        $fh = fopen($cacheFile, 'r');
        //$cacheTime = trim(fgets($fh));
        
        $cacheTime = filemtime($cacheFile);

        // if data was cached recently, return cached data
        if ($cacheTime > strtotime('-60 minutes')) {
            $object_cached = file_get_contents($cacheFile);
            
            $output = json_decode( $object_cached );
            
            //echo "Cache Read";
            return $output;
        }

        // else delete cache file
        fclose($fh);
        unlink($cacheFile);
    }

    
    $output = $function($object);
    
    $new_object_json = json_encode( $output );
	
    $fh = fopen($cacheFile, 'w');
    fwrite($fh, $new_object_json);
    fclose($fh);
	
	//echo "File Made";
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
		
		$boxes[$i]['type'] =	$post->type;
		$boxes[$i]['id'] =		$post->id;
		$boxes[$i]['image_url'] = $post->images->low_resolution->url;
		//$boxes[$i]['date'] 		= date( "D n/j" , strtotime( $post->created_time ) );
		$boxes[$i]['text'] = 	$post->caption->text;
		$boxes[$i]['link'] = 	$post->link;
		
		$i++;
	}
	
	return $boxes;
	
	
}


function boxesVimeo($boxes) {

	$output = new stdClass();
	
	$vid_url = parse_url($boxes->source);
	
	$json_url = 'http://vimeo.com/api/v2'.$boxes->source.'/videos.json';
	
	//$json = json_decode( getJson($json_url) );
	
	$json = json_decode( file_get_contents($json_url) );
	
	$i=0;
	foreach ($json as $post) {
		
		$details = new stdClass();
		
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
		
		$v_box = new stdClass;
		
		$v_box->type 		= "video";
		$v_box->id 		= $post->id;
		$v_box->image_url = $post->thumbnail_large;
		$v_box->date 		= $date;
		$v_box->title 	= $title;
		$v_box->text 		= $title;
		$v_box->desc 		= $desc[0];
		$v_box->link 		= $link;
		
		
		$boxes->boxes[$i] = $v_box;
		
		if( $i >= $boxes->amount - 1 ) break;
		
		$i++;
	}
	
	$output = $boxes;
	
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
	
	$filters = array(
		'cat_ids'  => array(
			5
		),
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
			
			$date = date( "F jS" , strtotime( $date_raw ) );
			
			$e_box->date				= $date;
			$e_box->date_unix			= strtotime($date_raw);
			$e_box->id					= $id;
			$e_box->title				= $title;
			$e_box->image_url			= getThumb( $id );
			$e_box->type				= "event";
			$e_box->color				= get_field('page_color', $id);
			$e_box->link				= $boxes->site_url."/?p=".$id;
			
			$boxes->boxes[$e] = $e_box;
			
			$e++;
		}
	}
	
	//Sort by date
	function date_sort($a, $b) {
	  return strcmp($a->date_unix, $b->date_unix); //only doing string comparison
	}
	usort( $boxes->boxes, 'date_sort');
	
	$output = $boxes;
	
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
		
		$boxes->boxes = false;
		
		$boxes->site_url = site_url();
		
		foreach ($atts as $key => $value) {$boxes->{$key} = $value; }//Convert Shortcode attributes to object values
		
		$parsed_classes = explode(' ', $class);
		
		$props = array();
		
		foreach( $parsed_classes as $prop ) {
			$props[$prop] = true;
		}
		
		//$debug = $props;
		
		$boxes->props = $props;
		
		
		//Feed Type
		switch ($type) {
		    case "instagram":
		        $boxes->boxes = boxesInstagram($boxes);
		        
		        $target = "_blank";
		        
		        break;
		    case "vimeo":
		        
		        //$boxes = boxesVimeo($boxes);
		        
		        $vid_url = parse_url($boxes->source);
		        
		        $boxes->source = $vid_url['path'];
		        
		        $boxes->id = preg_replace('/[^\da-z]/i', '', $vid_url['path'] );
		        
		        
		        $boxes = cacheHandler($boxes, "boxesVimeo");
		        
		        $target = "_self";
		        
		        break;
		    case "events":
		    	
		    	$boxes->id = $source;
				
				$boxes = cacheHandler($boxes, "boxesEvents");
				
				//$boxes->boxes = boxesEvents( $boxes );
				
				//$debug = $boxes;
				
		        //debug( $query );
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
		
		<?php 
			
			if( isset( $props['double-stacked'] ) ){
				
				$boxes->show = $boxes->show * 2;
				
			}
			
		?>
			<div id="boxes-<?php echo $type; ?>-<?php echo $boxes->id; ?>" class="box-boxes boxes-<?php echo $type; ?> <?php echo $class; ?>">


			<div class="frame" data-show="<?php echo $boxes->show; ?>">
				<ul class="easecubic">
					<?php if( $boxes->boxes ): foreach($boxes->boxes as $key => $box){ ?>
					
						<?php 
							if( isset( $props['date-format-human'] ) ){
								
								$normal_date = strtotime( $box->date );
								
								if( strtotime( $box->text ) ) $normal_date = strtotime( $box->text );
								
								$box->text = ago( $normal_date );
								
							}
							
							$box->srcType = "data-lazy";
							if( $key < $boxes->show ) $box->srcType = "src";//if is showing don't lazyload
						?>
						
						
							<?php if( !isset( $props['double-stacked'] ) || $key % 2 != 0 || !$key ){ ?><li><?php } ?>
								
								<div id="box-<?php echo $type; ?>-<?php echo $box->id; ?>" class="box-box box-<?php echo $box->type; ?> easecubic" style="<?php 
								//BG Color Overlay
								if( isset( $box->color ) ): 
									?>background: <?php echo $box->color; ?>; <?php //#000000
								endif; ?>">
								
									<a href="<?php echo $box->link; ?>" target="<?php echo $target; ?>" >
										<div class="box-image">
											<img class="easecubic" <?php echo $box->srcType; ?>="<?php echo $box->image_url; ?>" alt="<?php echo $box->text; ?>" >
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
								
							<?php if( !isset( $props['double-stacked'] ) || $key % 2 == 0 || !$key  ){ ?></li><?php } ?>
						
						
					<?php } endif; ?>
				</ul>
			</div>
			
		</div>
		<?php
		$content = ob_get_clean();
		
		debug( $debug );
			
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