<?php

function debug( $thing ) {
	
	if( !isset( $thing ) ) return "That's not a set value";
	
	if( $thing == null ) return "He's Null Jim";
	
	ob_start(); ?><pre><?php print_r($thing); ?></pre><?php $output = ob_get_clean();
	
	echo $output;
	
}

//$current_id = get_the_ID();

function getImage($id = false, $size = 'thumb-hd') {
	
	if( !$id ) $id = get_the_ID();
	
	$thumb_id = get_post_thumbnail_id($id);
	$thumb_url_array = wp_get_attachment_image_src($thumb_id, $size, true);
	
	if (strpos($thumb_url_array[0],'/wp-includes/images/media/default') !== false) {
		$filler_id = intval( substr($id, -2) );
		$thumb_url_array = wp_get_attachment_image_src( getFillerImage( $filler_id ) , $size, true);
	}
	
	$thumb_url = $thumb_url_array[0];
	
	return $thumb_url;
}

function getFillerImage( $digit ) {
	
	$fillers = array(
		30,
		9548,
		9549,
		47,
		54,
		9547,
		9546,
		9545,
		9544,
		9543,
		9542,
		9541,
		9540,
		9539,
		9538,
		9537,
		9536,
		9535,
		9534,
		9532,
		9530,
		9529,
		9522,
		9521,
		9520,
		9519,
		9518,
		9517,
		9516,
		9515,
		9514,
		9513,
		9512,
		9511,
		9510,
		9509,
		9508,
		9507,
		9506,
		9505,
		9504,
		9503,
		9502,
		9501,
		9500,
		9499,
		9498,
		9497,
		9496,
		9495,
		9494,
		9493,
		9492,
		9491,
		9490,
		9489,
		9488,
		9487,
		9486,
		9485,
		9484,
		9483,
		9482,
		9481,
		9480,
		9479,
		9478,
		9477,
		9476,
		9475,
		9474,
		9473,
		9472,
		9471,
		9470,
		9469,
		9468,
		9467,
		9466,
		9465,
		9464,
		9463,
		9462,
		9461,
		9460,
		9459,
		9458,
		9457,
		9456,
		9455,
		9454,
		9453,
		9452,
		9451,
		9450,
		9449,
		9448,
		9447,
		9446,
		9445,
		9552
	);
	
	return $fillers[$digit];
	
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

function parse_title($unparsed) {
	$parsed = strtr ( $unparsed , array ('|' => '<br />'));
	return $parsed;
}

function cacheHandler( $object ) {
    // cache files are created like cache/abcdef123456...
    
    //if no cache_time is set: 1 hour
    if( empty( $object->cache->cache_time ) ) $object->cache->cache_time = '-60 minutes';
    
    $function = $object->cache->function_name;
    
    $filename = $object->cache->function_name . "-" . preg_replace("/[^\da-z]/i", '', $object->id ).".json";
    if( $object->cache->cache_name ) $filename = $object->cache->cache_name."-".$filename;
    $cacheFile = 'cache' . DIRECTORY_SEPARATOR . $filename;
	
	if( array_key_exists( 'purge' , $_GET ) || $object->purge == true ) @unlink($cacheFile);
	
    if (file_exists($cacheFile)) {
        $fh = fopen($cacheFile, 'r');
        //$cacheTime = trim(fgets($fh));
        
        $cacheTime = filemtime($cacheFile);

        // if data was cached recently, return cached data
        if ($cacheTime > strtotime( $object->cache->cache_time )) {
            $object_cached = @file_get_contents($cacheFile);
            
            if( !empty( $object->cache->json ) ) return $object_cached;
            
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
    
    if( !empty( $object->cache->json ) ) return $new_object_json;
	
	//echo "File Made";
    return $output;
}

function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   
   $rgb = array($r, $g, $b);
   
/*
   $rgb = new stdClass;
   $rgb->red	= $r;
   $rgb->green	= $g;
   $rgb->blue	= $b;
*/
   return implode(",", $rgb); // returns the rgb values separated by commas
   //return $rgb; // returns an array with the rgb values
}

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


function removeWhitespace($buffer) {
	
	//$string = $buffer;
	
    //return preg_replace('~>\s+<~', '><', $buffer);
    
    //preg_replace_callback('~<([A-Z0-9]+) \K(.*?)>~i', function($m) {$replacement = preg_replace('~\s*~', '', $m[0]); return $replacement;}, $string);
    
    return preg_replace( '/>(\s|\n|\r)+</', '><', $buffer );
}


function parse_shortcode( $string ){
	
	$shortcode = new stdClass();
	
	$naked_shortcode = trim($string,"[]");
	if( !$naked_shortcode ) return false;
	$parts = explode(' ', $naked_shortcode);
	
	foreach( $parts as $key => $part ){
		if( $key == 0 ){
			$shortcode->name = $part;
			continue;//skip iteration
		}
		$attribute = explode('=', $part);
		$shortcode->{$attribute[0]} = trim($attribute[1],'"');
	}
	
	return $shortcode;
}

function custom_options() {
	
	$output = new stdClass();
	
	$options = get_field('custom_options');
	
	if( empty( $options ) ) return false;
	
	foreach( $options as $option ){
		
		if( $option['option'] == "" ) continue;
		
		$output->{$option['option']} = $option['value'];
	}
	
	return $output;
}


function countdownEvents( $object=false ) {
	
	$object = new stdClass();//Argument parsing can go here
	
	$events = array();
	
	$until = false;
	
	$chop_json = @file_get_contents("http://live.gutschurch.com/api/v1/events/current");//Get contents and supress warnings to allow for fallback
	
	if( $chop_json ) {
		
		$chop_json = json_decode( $chop_json );
		
		$countdownSecs = strtotime( $chop_json->response->item->eventStartTime ) - time();//D n/j ga
		
		$until = strtotime( $chop_json->response->item->eventStartTime );
		
		//$until_js = date( 'Y/m/d H:i:00', $until );
		
	} else {//Fallback to default service times
		
		$eventTime = 4500;
	
		$firstTue = strtotime('first Tuesday of this month 10am');
		
		$schedule = array(
		    'this Sunday 9am',
		    'this Sunday 11am',
		//	'this Sunday 6pm',
		    'this Wednesday 7pm'
		    );
		    
/*
		if(strtotime("now") > strtotime("-3 days", $firstTue)  && strtotime("now") < strtotime("+3 days", $firstTue) ) {
		
			$schedule = array(
		    	'this Sunday 9am',
			    'this Sunday 11am',
		//	    'this Sunday 6pm',
			    'this Tuesday 10am',
			    'this Wednesday 7pm'
		    	);
		    	
		}
*/
		
		
		$current_time = strtotime('now');
		foreach ($schedule as &$val) {
		    $val = strtotime($val);
		    // 5400 = 1.5hrs | Time until ends of next service
		    //$val = $val + $eventTime;
		    // fix schedule to next week if time resolved to the past
		    if ($val - $current_time < 0) $val += 604800;
		    }
		sort($schedule);
		//$until = $schedule[0] - $current_time;
		
		$until = $schedule[0]; 
		
	}
	
	$event = new stdClass();
	
	$event->start_time = $until;
	
	$events[0] = $event;
	
	return $events;
	
}


function isLive() {
	
	$countdown = new stdClass();
	$countdown->cache = new stdClass();
	
	//Define countdown options
	$countdown->cache->function_name =	"countdownEvents";
	$countdown->cache->cache_time =		'-15 minutes';
	$countdown->cache->cache_name =		"countdown";
    
    //Get any events
    $countdown->objects = countdownEvents();
    
    //Parse next event date into js friendly date
    //$until_js = date( 'Y/m/d H:i:00', $countdown->objects[0]->start_time );
    
    $objects = $countdown->objects;
    
    $event = $objects[0];
    
    //strtotime("-30 minutes")
    
    //$event->start_time;
    
    //echo ( $event->start_time > strtotime($event->start_time,"-15 minutes") );
    
    $countdown->now = date( 'r', strtotime('now') );
    
    $countdown->event = date( 'r', $event->start_time );
    
    $countdown->isAfterPre = ( strtotime('now') > strtotime($event->start_time,"-15 minutes") );
    
    $countdown->isBeforeEnd = ( strtotime('now') > strtotime($event->start_time,"+90 minutes") );
    
    $countdown->isLive = ( $countdown->isAfterPre && $countdown->isBeforeEnd );
    
/*
    if( $event->start_time > strtotime($event->start_time,"-30 minutes") ){
	     
    }
*/
	
	return $countdown->isLive;
}


function currentServiceLink() {
	
	$output = false;
	
	if( isLive() ){
		
		$output = site_url().'/live';
		
	} else {
		
		$output = site_url().'/watch';
		
	}
	
	return $output;
}

function social_media_profiles($strapped=0) {
	
	//HTML Output
	$output = "";
	
	//Current Pages Fields
	$options = custom_options();
	
	//Option check
	if( empty( $options ) ) return false;
	
	//Types of Profiles and Order
	$profiles_list = array(
		"facebook",
		"twitter",
		"instagram",
		"email",
		"pinterest"
	);
	
	
	//Filter Profiles for current page
	$profiles = array();
	
	foreach( $profiles_list as $profile ){
		//if it isn't set skip
		if( empty( $options->{$profile} ) ) continue;
		array_push( $profiles, $profile );
	}
	
	//Profiles check
	if( empty( $profiles ) ) return false;
	
	$profiles_count = count($profiles);
	
	
	//Loop
	$p=0;
	foreach( $profiles as $profile ){
		
		//12
		
		if ($profiles_count & 1) {//if even
			
			$po = ( 12 - ( $profiles_count * 2 ) ) / 2;
			
			$pos = $po;
			
			$pw = 2;
			
		} else {//if odd
			
			$po = ( 12 - $profiles_count ) / 2;
			
			$pos = ( 12 - ($profiles_count * 2 ) ) / 2;
			
			$pw = 1;
			
		}
		
		$classes = "social-bar-item";
		
		if( $strapped ) $classes .= " col-sm-".$pw." col-xs-2";
		
		//Add offset to first
		if( $strapped && !$p ) $classes .= " col-sm-offset-".$po." col-xs-offset-".$pos;
		
		$output .= '<div class="'.$classes.'"><a href="'.$options->{$profile}.'"><i class="gc-'.$profile.'"></i></a></div>';
		
	$p++;
	}
	
	//If $p is still 0
	if( !$p ) return false;
	
	return $output;
}




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