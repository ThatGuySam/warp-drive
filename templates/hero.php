<?php
	
	global $hero;
	
	if( !isset( $hero ) ) $hero = new stdClass();
	
	$hero->classes = array();
	
	//debug( $hero );
	
	$hero->kind = "text";
	
	$hero->text = get_the_title();
	
	$hero->index = 0;
	
	$hero->srcType = "src";
	
	$hero->heroes = false;
	
	$hero->heroesCount = 0;
	
	if( get_field('heroes') ) {
		$hero->heroes = new stdClass();
		$hero->heroes = get_field('heroes');
	}
	
	array_push( $hero->classes, get_post_type() );
	
	if( has_post_thumbnail() || $hero->heroes ){
	
		$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "full" );
		
		$hero->kind = "media";
		
		$hero->media = "image";
		
		$hero->src = $thumbnail_src[0];
		
		
		if( isset( $hero->heroes[0]['image'] ) ){

			$hero->attachment_id = $hero->heroes[0]['image'];
			
			$image_attachment = wp_get_attachment_image_src($hero->attachment_id, '720');
			
			$hero->src = $image_attachment[0];
		}
		
		
		if( get_post_type() == "ai1ec_event" ){// if it's a shortcode
		
			$hero->kind = "event";
			
			array_push( $hero->classes, "media" );
			
		}
		
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
	
	
	array_push( $hero->classes, $hero->kind );
	
	
	if( $hero->heroesCount > 1 && $hero->kind !== "shortcode" ) array_push( $hero->classes, "slick" );
	
	
?>
<div id="hero" class="hero-container <?php foreach ($hero->classes as &$class) if($class !== "") echo "hero-".$class." ";//echo all classes ?>container-fluid nopadding dark" data-hero-count="<?php echo $hero->heroesCount; ?>" >
	
<!--
	
	Hero Priorities
	
	Text < Image < Video < Organism
	
-->
	
	<div class="hero-section row">
	
	<?php if(get_field('heroes')): while(has_sub_field('heroes')): ?>
		
			<?php
				
				//Hide After
				if( get_sub_field('hide_after') && ( get_sub_field('hide_after') / 1000 + (24*60*60) ) < date('U') )//if date is set and it has passed
					continue;//Skip
				
				if( $hero->index ) $hero->srcType = 'src="'.get_template_directory_uri().'/assets/img/blank.gif" data-lazy';//if it's anything but the first lazy load it
				
				//Setup Image
				$hero->attachment_id = get_sub_field('image');
				
				$image_attachment = wp_get_attachment_image_src($hero->attachment_id, '720');
				
				$hero->src = $image_attachment[0];
				
				
				$hero->link = get_sub_field('link');
				
				//Setup Foreground
				$hero->text = $hero->title;
				if( get_sub_field('title') ) $hero->text = get_sub_field('title');
				$hero->text = trim( $hero->text );
				
				$hero->heroes[$hero->index]['shortcode'] = parse_shortcode( $hero->text );
				
				if($hero->heroes[$hero->index]['shortcode']) {
				    $hero->heroes[$hero->index]['kind'] = "shortcode";
				}
				
			?>
			
			
			<?php 
				echo heroOrganism($hero); 
				$hero->index++;
			?>
			
		
		<?php endwhile; 
		
		else: ?>
		
			<?php echo heroOrganism($hero); ?>
		
		<?php endif; ?>
		
	</div>
	
	
	<?php if( $hero->kind !== "text"): ?>
		
		<a href="#content" class="scrollto">
			<div class="read-more hidden-sm hidden-xs">
				<span class="read-more-text">Scroll</span>
				<br>
				<span><i class="down-arrow"></i></span>
			</div>
		</a>
		
	<?php endif; ?>
	
	
	
</div>