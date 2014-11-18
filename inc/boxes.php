<?php

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