<?php

function debug( $thing ) {
	
	$output_data = $thing;
	
	if( !isset( $thing ) ) $output_data = 'That\'s not a set value';
	
	//if( $thing === null ) $output_data = 'He\'s Null Jim';
	
	if( $thing === false ) $output_data = 'Returned False';
	
	if( $thing === 0 ) $output_data = 'Returned Zero';
	
	ob_start(); ?><pre><?php var_dump($thing); ?></pre><?php $output = ob_get_clean();
	
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
    
    $filename = $object->cache->function_name . '-' . preg_replace('/[^\da-z]/i', '', $object->id ).'.json';
    if( $object->cache->cache_name ) $filename = $object->cache->cache_name.'-'.$filename;
    $cacheFile = 'cache' . DIRECTORY_SEPARATOR . $filename;
	
	//if( array_key_exists( 'purge' , $_GET ) || $object->purge === true )
	
	if( array_key_exists( 'purge' , $_GET ) ) {
	
		//w3tc_pgcache_flush();
		@unlink($cacheFile);
	}
	
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
        @unlink($cacheFile);
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
   $hex = trim($hex, '#');

   if(strlen($hex) === 3) {
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
   return implode(',', $rgb); // returns the rgb values separated by commas
   //return $rgb; // returns an array with the rgb values
}

function getContrastingColor($hexcolor){
    //24ways.org/2010/calculating-color-contrast/
    
    //Simple
    //return (hexdec(trim($hexcolor,'#')) > 0xffffff/2) ? '323232':'d8d8d8';
    
    //YIQ
    $hexcolor = trim($hexcolor,'#');//Strip pounds
    $r = hexdec(substr($hexcolor,0,2));
	$g = hexdec(substr($hexcolor,2,2));
	$b = hexdec(substr($hexcolor,4,2));
	$yiq = (($r*299)+($g*587)+($b*114))/1000;
	return ($yiq >= 128) ? '323232' : 'd8d8d8';
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

function clean_user_url($user_url) {
	
	//Defaults
	$scheme = 'http';
	$host = 'gutschurch.com';
	$path
	= $query
	= $fragment
	= '';
	
	if (!preg_match("~^(?:f|ht)tps?://~i", $user_url)) $user_url = "http://" . $user_url;
	
	$url = parse_url($user_url);
	
	extract($url);
	
	return $scheme.'://'.$host.$path.'?'.$query.'#'.$fragment;
	
/*
	[scheme] => http
    [host] => hostname
    [user] => username
    [pass] => password
    [path] => /path
    [query] => arg=value
    [fragment] => anchor
*/

}


function parse_shortcode( $raw_string ){
	
	$string = trim($raw_string);
	if( empty( $string ) ) return false;
	
	preg_match('/\[([^\]]*)\]/', $string, $matches);
	if( empty( $matches[1] ) ) return false;
	$naked_shortcode = $matches[1];
	if( empty($naked_shortcode) ) return false;
	
	$shortcode = new stdClass();
	$parts = explode(' ', $naked_shortcode);
	
	foreach( $parts as $key => $part ){
		if( !$key ){
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
		
		if( $option['option'] === '' ) continue;
		
		$output->{$option['option']} = $option['value'];
	}
	
	return $output;
}

function countdownEvents( $object=false ) {
	
	$object = new stdClass();//Argument parsing can go here
	
	$events = array();
	
	$until = false;
	
	
	//live.gutschurch.com/api/v1/events/current?expand=event
	
	//Live Test
	//samcarlton.s3.amazonaws.com/current.json
	$chop_json = @file_get_contents('http://live.gutschurch.com/api/v1/events/current?expand=event');//Get contents and supress warnings to allow for fallback
	
	if( $chop_json ) {
		
		$chop_json = json_decode( $chop_json );
		
		//debug( $chop_json );
		
		$countdownSecs = strtotime( $chop_json->response->item->eventStartTime ) - time();//D n/j ga
		
		$until = strtotime( $chop_json->response->item->eventStartTime );
		
		//$until_js = date( 'Y/m/d H:i:00', $until );
	
	//Fallback to default service times
	} else {
		
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
	
	//return true;
	
	$countdown = new stdClass();
	$countdown->cache = new stdClass();
	$min = 60;
	
	//Define countdown options
	$countdown->cache->function_name =	'countdownEvents';
	$countdown->cache->cache_time =		'-15 minutes';
	$countdown->cache->cache_name =		'countdown';
    
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
    
    
    $countdown->eventPre = date( 'U', $event->start_time - ( 15*$min ) );
    
    $countdown->eventEnd = date( 'U', $event->start_time + ( 90*$min ) );
    
    
    $countdown->isAfterPre = ( strtotime('now') > $countdown->eventPre );
    
    $countdown->isBeforeEnd = ( strtotime('now') > $countdown->eventEnd );
    
    $countdown->isLive = ( $countdown->isAfterPre && $countdown->isBeforeEnd );
	
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


/*
function newestLink( $type='post', $id=0 ) {
	
	
	
	return $output;
}
*/

function social_media_profiles($strapped=0,$profiles=0) {
	
	//HTML Output
	$output = '';
	
	//Current Pages Fields
	$options = custom_options();
	
	//Option check
	if( empty( $options ) ) return false;
	
	//Types of Profiles and Order
	$profiles_list = array(
		'facebook',
		'twitter',
		'instagram',
		'pinterest',
		'vimeo',
		'email'
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
		
		$classes = 'social-bar-item';
		
		if( $strapped ) $classes .= ' col-sm-'.$pw.' col-xs-2';
		
		//Add offset to first
		if( $strapped && !$p ) $classes .= ' col-sm-offset-'.$po.' col-xs-offset-'.$pos;
		
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
        if($menu_item->menu_item_parent===0){
            $count++;
        }
    }
    return $count;
}


function ago($time){
	
	$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
	$lengths = array('60','60','24','7','4.35','12','10');
	
	$now = time();
	
	   $difference     = $now - $time;
	   $tense         = 'ago';
	
	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
	   $difference /= $lengths[$j];
	}
	
	$difference = round($difference);
	
	if($difference != 1) {
	   $periods[$j].= 's';
	}
	
	$output = "$difference $periods[$j] ago";
	
	if( $time > strtotime('-14 day') ) $output = 'Last week';
	
	if( $time > strtotime('-7 day') ) $output = 'This week';
	
	return $output;//." ".date('r',$time);
}


function webFontLoader() {
	
	ob_start(); ?>
	
		<style type="text/css">
			// Prevent FOUT
			
			.tth-logo > a:before {
				opacity: 0;
			}
			.wf-inactive .tth-logo > a:before {
				opacity: 1;
			}
			.wf-loading * {
				opacity: 0;
				visibility:hidden;
			}
		</style>
		
		<script type="text/javascript">
		//Namespace
		window.gc = window.gc || {}; gc.pageScripts = [];
		
		WebFontConfig = {
			typekit: { id: 'rxw8wmu' },
			custom: {
				families: ['gc-icon'],
				urls: ['//tougherthanhell.s3.amazonaws.com/css/gc-icon.css']
			}
		};
		
			/* Web Font Loader v1.5.10 - (c) Adobe Systems, Google. License: Apache 2.0 */
;(function(window,document,undefined){var k=this;function l(a,b){var c=a.split("."),d=k;c[0]in d||!d.execScript||d.execScript("var "+c[0]);for(var e;c.length&&(e=c.shift());)c.length||void 0===b?d=d[e]?d[e]:d[e]={}:d[e]=b}function aa(a,b,c){return a.call.apply(a.bind,arguments)}
function ba(a,b,c){if(!a)throw Error();if(2<arguments.length){var d=Array.prototype.slice.call(arguments,2);return function(){var c=Array.prototype.slice.call(arguments);Array.prototype.unshift.apply(c,d);return a.apply(b,c)}}return function(){return a.apply(b,arguments)}}function n(a,b,c){n=Function.prototype.bind&&-1!=Function.prototype.bind.toString().indexOf("native code")?aa:ba;return n.apply(null,arguments)}var q=Date.now||function(){return+new Date};function s(a,b){this.K=a;this.w=b||a;this.D=this.w.document}s.prototype.createElement=function(a,b,c){a=this.D.createElement(a);if(b)for(var d in b)b.hasOwnProperty(d)&&("style"==d?a.style.cssText=b[d]:a.setAttribute(d,b[d]));c&&a.appendChild(this.D.createTextNode(c));return a};function t(a,b,c){a=a.D.getElementsByTagName(b)[0];a||(a=document.documentElement);a&&a.lastChild&&a.insertBefore(c,a.lastChild)}function ca(a,b){function c(){a.D.body?b():setTimeout(c,0)}c()}
function u(a,b,c){b=b||[];c=c||[];for(var d=a.className.split(/\s+/),e=0;e<b.length;e+=1){for(var f=!1,g=0;g<d.length;g+=1)if(b[e]===d[g]){f=!0;break}f||d.push(b[e])}b=[];for(e=0;e<d.length;e+=1){f=!1;for(g=0;g<c.length;g+=1)if(d[e]===c[g]){f=!0;break}f||b.push(d[e])}a.className=b.join(" ").replace(/\s+/g," ").replace(/^\s+|\s+$/,"")}function v(a,b){for(var c=a.className.split(/\s+/),d=0,e=c.length;d<e;d++)if(c[d]==b)return!0;return!1}
function w(a){var b=a.w.location.protocol;"about:"==b&&(b=a.K.location.protocol);return"https:"==b?"https:":"http:"}function x(a,b){var c=a.createElement("link",{rel:"stylesheet",href:b}),d=!1;c.onload=function(){d||(d=!0)};c.onerror=function(){d||(d=!0)};t(a,"head",c)}
function y(a,b,c,d){var e=a.D.getElementsByTagName("head")[0];if(e){var f=a.createElement("script",{src:b}),g=!1;f.onload=f.onreadystatechange=function(){g||this.readyState&&"loaded"!=this.readyState&&"complete"!=this.readyState||(g=!0,c&&c(null),f.onload=f.onreadystatechange=null,"HEAD"==f.parentNode.tagName&&e.removeChild(f))};e.appendChild(f);window.setTimeout(function(){g||(g=!0,c&&c(Error("Script load timeout")))},d||5E3);return f}return null};function z(a,b,c,d){this.R=a;this.Z=b;this.Ba=c;this.ra=d}l("webfont.BrowserInfo",z);z.prototype.sa=function(){return this.R};z.prototype.hasWebFontSupport=z.prototype.sa;z.prototype.ta=function(){return this.Z};z.prototype.hasWebKitFallbackBug=z.prototype.ta;z.prototype.ua=function(){return this.Ba};z.prototype.hasWebKitMetricsBug=z.prototype.ua;z.prototype.qa=function(){return this.ra};z.prototype.hasNativeFontLoading=z.prototype.qa;function A(a,b,c,d){this.c=null!=a?a:null;this.g=null!=b?b:null;this.B=null!=c?c:null;this.e=null!=d?d:null}var da=/^([0-9]+)(?:[\._-]([0-9]+))?(?:[\._-]([0-9]+))?(?:[\._+-]?(.*))?$/;A.prototype.compare=function(a){return this.c>a.c||this.c===a.c&&this.g>a.g||this.c===a.c&&this.g===a.g&&this.B>a.B?1:this.c<a.c||this.c===a.c&&this.g<a.g||this.c===a.c&&this.g===a.g&&this.B<a.B?-1:0};A.prototype.toString=function(){return[this.c,this.g||"",this.B||"",this.e||""].join("")};
function B(a){a=da.exec(a);var b=null,c=null,d=null,e=null;a&&(null!==a[1]&&a[1]&&(b=parseInt(a[1],10)),null!==a[2]&&a[2]&&(c=parseInt(a[2],10)),null!==a[3]&&a[3]&&(d=parseInt(a[3],10)),null!==a[4]&&a[4]&&(e=/^[0-9]+$/.test(a[4])?parseInt(a[4],10):a[4]));return new A(b,c,d,e)};function C(a,b,c,d,e,f,g,h){this.P=a;this.ja=c;this.ya=e;this.ia=g;this.m=h}l("webfont.UserAgent",C);C.prototype.getName=function(){return this.P};C.prototype.getName=C.prototype.getName;C.prototype.oa=function(){return this.ja};C.prototype.getEngine=C.prototype.oa;C.prototype.pa=function(){return this.ya};C.prototype.getPlatform=C.prototype.pa;C.prototype.na=function(){return this.ia};C.prototype.getDocumentMode=C.prototype.na;C.prototype.ma=function(){return this.m};C.prototype.getBrowserInfo=C.prototype.ma;function D(a,b){this.a=a;this.k=b}var ea=new C("Unknown",0,"Unknown",0,"Unknown",0,void 0,new z(!1,!1,!1,!1));
D.prototype.parse=function(){var a;if(-1!=this.a.indexOf("MSIE")||-1!=this.a.indexOf("Trident/")){a=E(this);var b=B(F(this)),c=null,d=null,e=G(this.a,/Trident\/([\d\w\.]+)/,1),f=H(this.k),c=-1!=this.a.indexOf("MSIE")?B(G(this.a,/MSIE ([\d\w\.]+)/,1)):B(G(this.a,/rv:([\d\w\.]+)/,1));""!=e?(d="Trident",B(e)):d="Unknown";a=new C("MSIE",0,d,0,a,0,f,new z("Windows"==a&&6<=c.c||"Windows Phone"==a&&8<=b.c,!1,!1,!!this.k.fonts))}else if(-1!=this.a.indexOf("Opera"))a:if(a="Unknown",c=B(G(this.a,/Presto\/([\d\w\.]+)/,
1)),B(F(this)),b=H(this.k),null!==c.c?a="Presto":(-1!=this.a.indexOf("Gecko")&&(a="Gecko"),B(G(this.a,/rv:([^\)]+)/,1))),-1!=this.a.indexOf("Opera Mini/"))c=B(G(this.a,/Opera Mini\/([\d\.]+)/,1)),a=new C("OperaMini",0,a,0,E(this),0,b,new z(!1,!1,!1,!!this.k.fonts));else{if(-1!=this.a.indexOf("Version/")&&(c=B(G(this.a,/Version\/([\d\.]+)/,1)),null!==c.c)){a=new C("Opera",0,a,0,E(this),0,b,new z(10<=c.c,!1,!1,!!this.k.fonts));break a}c=B(G(this.a,/Opera[\/ ]([\d\.]+)/,1));a=null!==c.c?new C("Opera",
0,a,0,E(this),0,b,new z(10<=c.c,!1,!1,!!this.k.fonts)):new C("Opera",0,a,0,E(this),0,b,new z(!1,!1,!1,!!this.k.fonts))}else/OPR\/[\d.]+/.test(this.a)?a=I(this):/AppleWeb(K|k)it/.test(this.a)?a=I(this):-1!=this.a.indexOf("Gecko")?(a="Unknown",b=new A,B(F(this)),b=!1,-1!=this.a.indexOf("Firefox")?(a="Firefox",b=B(G(this.a,/Firefox\/([\d\w\.]+)/,1)),b=3<=b.c&&5<=b.g):-1!=this.a.indexOf("Mozilla")&&(a="Mozilla"),c=B(G(this.a,/rv:([^\)]+)/,1)),b||(b=1<c.c||1==c.c&&9<c.g||1==c.c&&9==c.g&&2<=c.B),a=new C(a,
0,"Gecko",0,E(this),0,H(this.k),new z(b,!1,!1,!!this.k.fonts))):a=ea;return a};function E(a){var b=G(a.a,/(iPod|iPad|iPhone|Android|Windows Phone|BB\d{2}|BlackBerry)/,1);if(""!=b)return/BB\d{2}/.test(b)&&(b="BlackBerry"),b;a=G(a.a,/(Linux|Mac_PowerPC|Macintosh|Windows|CrOS|PlayStation|CrKey)/,1);return""!=a?("Mac_PowerPC"==a?a="Macintosh":"PlayStation"==a&&(a="Linux"),a):"Unknown"}
function F(a){var b=G(a.a,/(OS X|Windows NT|Android) ([^;)]+)/,2);if(b||(b=G(a.a,/Windows Phone( OS)? ([^;)]+)/,2))||(b=G(a.a,/(iPhone )?OS ([\d_]+)/,2)))return b;if(b=G(a.a,/(?:Linux|CrOS|CrKey) ([^;)]+)/,1))for(var b=b.split(/\s/),c=0;c<b.length;c+=1)if(/^[\d\._]+$/.test(b[c]))return b[c];return(a=G(a.a,/(BB\d{2}|BlackBerry).*?Version\/([^\s]*)/,2))?a:"Unknown"}
function I(a){var b=E(a),c=B(F(a)),d=B(G(a.a,/AppleWeb(?:K|k)it\/([\d\.\+]+)/,1)),e="Unknown",f=new A,f="Unknown",g=!1;/OPR\/[\d.]+/.test(a.a)?e="Opera":-1!=a.a.indexOf("Chrome")||-1!=a.a.indexOf("CrMo")||-1!=a.a.indexOf("CriOS")?e="Chrome":/Silk\/\d/.test(a.a)?e="Silk":"BlackBerry"==b||"Android"==b?e="BuiltinBrowser":-1!=a.a.indexOf("PhantomJS")?e="PhantomJS":-1!=a.a.indexOf("Safari")?e="Safari":-1!=a.a.indexOf("AdobeAIR")?e="AdobeAIR":-1!=a.a.indexOf("PlayStation")&&(e="BuiltinBrowser");"BuiltinBrowser"==
e?f="Unknown":"Silk"==e?f=G(a.a,/Silk\/([\d\._]+)/,1):"Chrome"==e?f=G(a.a,/(Chrome|CrMo|CriOS)\/([\d\.]+)/,2):-1!=a.a.indexOf("Version/")?f=G(a.a,/Version\/([\d\.\w]+)/,1):"AdobeAIR"==e?f=G(a.a,/AdobeAIR\/([\d\.]+)/,1):"Opera"==e?f=G(a.a,/OPR\/([\d.]+)/,1):"PhantomJS"==e&&(f=G(a.a,/PhantomJS\/([\d.]+)/,1));f=B(f);g="AdobeAIR"==e?2<f.c||2==f.c&&5<=f.g:"BlackBerry"==b?10<=c.c:"Android"==b?2<c.c||2==c.c&&1<c.g:526<=d.c||525<=d.c&&13<=d.g;return new C(e,0,"AppleWebKit",0,b,0,H(a.k),new z(g,536>d.c||536==
d.c&&11>d.g,"iPhone"==b||"iPad"==b||"iPod"==b||"Macintosh"==b,!!a.k.fonts))}function G(a,b,c){return(a=a.match(b))&&a[c]?a[c]:""}function H(a){if(a.documentMode)return a.documentMode};function J(a){this.xa=a||"-"}J.prototype.e=function(a){for(var b=[],c=0;c<arguments.length;c++)b.push(arguments[c].replace(/[\W_]+/g,"").toLowerCase());return b.join(this.xa)};function K(a,b){this.P=a;this.$=4;this.Q="n";var c=(b||"n4").match(/^([nio])([1-9])$/i);c&&(this.Q=c[1],this.$=parseInt(c[2],10))}K.prototype.getName=function(){return this.P};function L(a){return a.Q+a.$}function fa(a){var b=4,c="n",d=null;a&&((d=a.match(/(normal|oblique|italic)/i))&&d[1]&&(c=d[1].substr(0,1).toLowerCase()),(d=a.match(/([1-9]00|normal|bold)/i))&&d[1]&&(/bold/i.test(d[1])?b=7:/[1-9]00/.test(d[1])&&(b=parseInt(d[1].substr(0,1),10))));return c+b};function ga(a,b,c,d,e){this.d=a;this.p=b;this.T=c;this.j="wf";this.h=new J("-");this.ha=!1!==d;this.C=!1!==e}function M(a){if(a.C){var b=v(a.p,a.h.e(a.j,"active")),c=[],d=[a.h.e(a.j,"loading")];b||c.push(a.h.e(a.j,"inactive"));u(a.p,c,d)}N(a,"inactive")}function N(a,b,c){if(a.ha&&a.T[b])if(c)a.T[b](c.getName(),L(c));else a.T[b]()};function ha(){this.A={}};function O(a,b){this.d=a;this.H=b;this.t=this.d.createElement("span",{"aria-hidden":"true"},this.H)}
function P(a,b){var c=a.t,d;d=[];for(var e=b.P.split(/,\s*/),f=0;f<e.length;f++){var g=e[f].replace(/['"]/g,"");-1==g.indexOf(" ")?d.push(g):d.push("'"+g+"'")}d=d.join(",");e="normal";"o"===b.Q?e="oblique":"i"===b.Q&&(e="italic");c.style.cssText="display:block;position:absolute;top:0px;left:0px;visibility:hidden;font-size:300px;width:auto;height:auto;line-height:normal;margin:0;padding:0;font-variant:normal;white-space:nowrap;font-family:"+d+";"+("font-style:"+e+";font-weight:"+(b.$+"00")+";")}
function Q(a){t(a.d,"body",a.t)}O.prototype.remove=function(){var a=this.t;a.parentNode&&a.parentNode.removeChild(a)};function ja(a,b,c,d,e,f,g,h){this.aa=a;this.va=b;this.d=c;this.s=d;this.H=h||"BESbswy";this.m=e;this.J={};this.Y=f||3E3;this.da=g||null;this.G=this.F=null;a=new O(this.d,this.H);Q(a);for(var p in R)R.hasOwnProperty(p)&&(P(a,new K(R[p],L(this.s))),this.J[R[p]]=a.t.offsetWidth);a.remove()}var R={Ea:"serif",Da:"sans-serif",Ca:"monospace"};
ja.prototype.start=function(){this.F=new O(this.d,this.H);Q(this.F);this.G=new O(this.d,this.H);Q(this.G);this.za=q();P(this.F,new K(this.s.getName()+",serif",L(this.s)));P(this.G,new K(this.s.getName()+",sans-serif",L(this.s)));ka(this)};function la(a,b,c){for(var d in R)if(R.hasOwnProperty(d)&&b===a.J[R[d]]&&c===a.J[R[d]])return!0;return!1}
function ka(a){var b=a.F.t.offsetWidth,c=a.G.t.offsetWidth;b===a.J.serif&&c===a.J["sans-serif"]||a.m.Z&&la(a,b,c)?q()-a.za>=a.Y?a.m.Z&&la(a,b,c)&&(null===a.da||a.da.hasOwnProperty(a.s.getName()))?S(a,a.aa):S(a,a.va):ma(a):S(a,a.aa)}function ma(a){setTimeout(n(function(){ka(this)},a),25)}function S(a,b){a.F.remove();a.G.remove();b(a.s)};function T(a,b,c,d){this.d=b;this.u=c;this.U=0;this.fa=this.ca=!1;this.Y=d;this.m=a.m}function na(a,b,c,d,e){c=c||{};if(0===b.length&&e)M(a.u);else for(a.U+=b.length,e&&(a.ca=e),e=0;e<b.length;e++){var f=b[e],g=c[f.getName()],h=a.u,p=f;h.C&&u(h.p,[h.h.e(h.j,p.getName(),L(p).toString(),"loading")]);N(h,"fontloading",p);h=null;h=new ja(n(a.ka,a),n(a.la,a),a.d,f,a.m,a.Y,d,g);h.start()}}
T.prototype.ka=function(a){var b=this.u;b.C&&u(b.p,[b.h.e(b.j,a.getName(),L(a).toString(),"active")],[b.h.e(b.j,a.getName(),L(a).toString(),"loading"),b.h.e(b.j,a.getName(),L(a).toString(),"inactive")]);N(b,"fontactive",a);this.fa=!0;oa(this)};
T.prototype.la=function(a){var b=this.u;if(b.C){var c=v(b.p,b.h.e(b.j,a.getName(),L(a).toString(),"active")),d=[],e=[b.h.e(b.j,a.getName(),L(a).toString(),"loading")];c||d.push(b.h.e(b.j,a.getName(),L(a).toString(),"inactive"));u(b.p,d,e)}N(b,"fontinactive",a);oa(this)};function oa(a){0==--a.U&&a.ca&&(a.fa?(a=a.u,a.C&&u(a.p,[a.h.e(a.j,"active")],[a.h.e(a.j,"loading"),a.h.e(a.j,"inactive")]),N(a,"active")):M(a.u))};function U(a){this.K=a;this.v=new ha;this.Aa=new D(a.navigator.userAgent,a.document);this.a=this.Aa.parse();this.V=this.W=0;this.M=this.N=!0}
U.prototype.load=function(a){var b=a.context||this.K;this.d=new s(this.K,b);this.N=!1!==a.events;this.M=!1!==a.classes;var b=new ga(this.d,b.document.documentElement,a,this.N,this.M),c=[],d=a.timeout;b.C&&u(b.p,[b.h.e(b.j,"loading")]);N(b,"loading");var c=this.v,e=this.d,f=[],g;for(g in a)if(a.hasOwnProperty(g)){var h=c.A[g];h&&f.push(h(a[g],e))}c=f;this.V=this.W=c.length;a=new T(this.a,this.d,b,d);g=0;for(d=c.length;g<d;g++)e=c[g],e.L(this.a,n(this.wa,this,e,b,a))};
U.prototype.wa=function(a,b,c,d){var e=this;d?a.load(function(a,b,d){pa(e,c,a,b,d)}):(a=0==--this.W,this.V--,a&&0==this.V?M(b):(this.M||this.N)&&na(c,[],{},null,a))};function pa(a,b,c,d,e){var f=0==--a.W;(a.M||a.N)&&setTimeout(function(){na(b,c,d||null,e||null,f)},0)};function qa(a,b,c){this.S=a?a:b+ra;this.q=[];this.X=[];this.ga=c||""}var ra="//fonts.googleapis.com/css";qa.prototype.e=function(){if(0==this.q.length)throw Error("No fonts to load!");if(-1!=this.S.indexOf("kit="))return this.S;for(var a=this.q.length,b=[],c=0;c<a;c++)b.push(this.q[c].replace(/ /g,"+"));a=this.S+"?family="+b.join("%7C");0<this.X.length&&(a+="&subset="+this.X.join(","));0<this.ga.length&&(a+="&text="+encodeURIComponent(this.ga));return a};function sa(a){this.q=a;this.ea=[];this.O={}}
var ta={latin:"BESbswy",cyrillic:"&#1081;&#1103;&#1046;",greek:"&#945;&#946;&#931;",khmer:"&#x1780;&#x1781;&#x1782;",Hanuman:"&#x1780;&#x1781;&#x1782;"},ua={thin:"1",extralight:"2","extra-light":"2",ultralight:"2","ultra-light":"2",light:"3",regular:"4",book:"4",medium:"5","semi-bold":"6",semibold:"6","demi-bold":"6",demibold:"6",bold:"7","extra-bold":"8",extrabold:"8","ultra-bold":"8",ultrabold:"8",black:"9",heavy:"9",l:"3",r:"4",b:"7"},va={i:"i",italic:"i",n:"n",normal:"n"},wa=/^(thin|(?:(?:extra|ultra)-?)?light|regular|book|medium|(?:(?:semi|demi|extra|ultra)-?)?bold|black|heavy|l|r|b|[1-9]00)?(n|i|normal|italic)?$/;
sa.prototype.parse=function(){for(var a=this.q.length,b=0;b<a;b++){var c=this.q[b].split(":"),d=c[0].replace(/\+/g," "),e=["n4"];if(2<=c.length){var f;var g=c[1];f=[];if(g)for(var g=g.split(","),h=g.length,p=0;p<h;p++){var m;m=g[p];if(m.match(/^[\w-]+$/)){m=wa.exec(m.toLowerCase());var r=void 0;if(null==m)r="";else{r=void 0;r=m[1];if(null==r||""==r)r="4";else var ia=ua[r],r=ia?ia:isNaN(r)?"4":r.substr(0,1);m=m[2];r=[null==m||""==m?"n":va[m],r].join("")}m=r}else m="";m&&f.push(m)}0<f.length&&(e=f);
3==c.length&&(c=c[2],f=[],c=c?c.split(","):f,0<c.length&&(c=ta[c[0]])&&(this.O[d]=c))}this.O[d]||(c=ta[d])&&(this.O[d]=c);for(c=0;c<e.length;c+=1)this.ea.push(new K(d,e[c]))}};function V(a,b){this.a=(new D(navigator.userAgent,document)).parse();this.d=a;this.f=b}var xa={Arimo:!0,Cousine:!0,Tinos:!0};V.prototype.L=function(a,b){b(a.m.R)};V.prototype.load=function(a){var b=this.d;"MSIE"==this.a.getName()&&1!=this.f.blocking?ca(b,n(this.ba,this,a)):this.ba(a)};
V.prototype.ba=function(a){for(var b=this.d,c=new qa(this.f.api,w(b),this.f.text),d=this.f.families,e=d.length,f=0;f<e;f++){var g=d[f].split(":");3==g.length&&c.X.push(g.pop());var h="";2==g.length&&""!=g[1]&&(h=":");c.q.push(g.join(h))}d=new sa(d);d.parse();x(b,c.e());a(d.ea,d.O,xa)};function W(a,b){this.d=a;this.f=b;this.o=[]}W.prototype.I=function(a){var b=this.d;return w(this.d)+(this.f.api||"//f.fontdeck.com/s/css/js/")+(b.w.location.hostname||b.K.location.hostname)+"/"+a+".js"};
W.prototype.L=function(a,b){var c=this.f.id,d=this.d.w,e=this;c?(d.__webfontfontdeckmodule__||(d.__webfontfontdeckmodule__={}),d.__webfontfontdeckmodule__[c]=function(a,c){for(var d=0,p=c.fonts.length;d<p;++d){var m=c.fonts[d];e.o.push(new K(m.name,fa("font-weight:"+m.weight+";font-style:"+m.style)))}b(a)},y(this.d,this.I(c),function(a){a&&b(!1)})):b(!1)};W.prototype.load=function(a){a(this.o)};function X(a,b){this.d=a;this.f=b;this.o=[]}X.prototype.I=function(a){var b=w(this.d);return(this.f.api||b+"//use.typekit.net")+"/"+a+".js"};X.prototype.L=function(a,b){var c=this.f.id,d=this.d.w,e=this;c?y(this.d,this.I(c),function(a){if(a)b(!1);else{if(d.Typekit&&d.Typekit.config&&d.Typekit.config.fn){a=d.Typekit.config.fn;for(var c=0;c<a.length;c+=2)for(var h=a[c],p=a[c+1],m=0;m<p.length;m++)e.o.push(new K(h,p[m]));try{d.Typekit.load({events:!1,classes:!1})}catch(r){}}b(!0)}},2E3):b(!1)};
X.prototype.load=function(a){a(this.o)};function Y(a,b){this.d=a;this.f=b;this.o=[]}Y.prototype.L=function(a,b){var c=this,d=c.f.projectId,e=c.f.version;if(d){var f=c.d.w;y(this.d,c.I(d,e),function(e){if(e)b(!1);else{if(f["__mti_fntLst"+d]&&(e=f["__mti_fntLst"+d]()))for(var h=0;h<e.length;h++)c.o.push(new K(e[h].fontfamily));b(a.m.R)}}).id="__MonotypeAPIScript__"+d}else b(!1)};Y.prototype.I=function(a,b){var c=w(this.d),d=(this.f.api||"fast.fonts.net/jsapi").replace(/^.*http(s?):(\/\/)?/,"");return c+"//"+d+"/"+a+".js"+(b?"?v="+b:"")};
Y.prototype.load=function(a){a(this.o)};function Z(a,b){this.d=a;this.f=b}Z.prototype.load=function(a){var b,c,d=this.f.urls||[],e=this.f.families||[],f=this.f.testStrings||{};b=0;for(c=d.length;b<c;b++)x(this.d,d[b]);d=[];b=0;for(c=e.length;b<c;b++){var g=e[b].split(":");if(g[1])for(var h=g[1].split(","),p=0;p<h.length;p+=1)d.push(new K(g[0],h[p]));else d.push(new K(g[0]))}a(d,f)};Z.prototype.L=function(a,b){return b(a.m.R)};var $=new U(k);$.v.A.custom=function(a,b){return new Z(b,a)};$.v.A.fontdeck=function(a,b){return new W(b,a)};$.v.A.monotype=function(a,b){return new Y(b,a)};$.v.A.typekit=function(a,b){return new X(b,a)};$.v.A.google=function(a,b){return new V(b,a)};k.WebFont||(k.WebFont={},k.WebFont.load=n($.load,$),k.WebFontConfig&&$.load(k.WebFontConfig));})(this,document);	
		</script>
	
	<?php $output = ob_get_clean();
	
	echo $output;
	
}
// Add hook for front-end <head></head>
add_action('wp_head', 'webFontLoader');

