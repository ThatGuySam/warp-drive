<?php

function debug( $thing ) {
	
	if( $thing == null || $thing == false ) return;
	
	ob_start(); ?><pre><?php print_r($thing); ?></pre><?php $output = ob_get_clean();
	
	echo $output;
	
}

//$current_id = get_the_ID();

function getThumb($id = false, $size = 'thumb-hd') {
	
	if( !$id ) $id = get_the_ID();
	
	$thumb_id = get_post_thumbnail_id($id);
	$thumb_url_array = wp_get_attachment_image_src($thumb_id, $size, true);
	$thumb_url = $thumb_url_array[0];
	
	return $thumb_url;
	
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
		
		$output .= '<div class="'.$classes.'"><a href="'.$options->{$profile}.'"><i class="fa fa-'.$profile.'"></i></a></div>';
		
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