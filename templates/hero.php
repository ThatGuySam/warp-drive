<?php
	
	$hero = new stdClass();
	
	$hero->classes = array();
	
	$hero->kind = "text";
	
	$hero->text = get_the_title();
	
	$hero->index = 0;
	
	$hero->srcType = "src";
	
	if( has_post_thumbnail() ){
	
		$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "full" );
		
		$hero->kind = "media";
		
		$hero->media = "image";
		
		$hero->src = $thumbnail_src[0];
	}
	
	
	array_push( $hero->classes, $hero->kind );
	
	
	if(get_field('heroes')) array_push( $hero->classes, "slick" );
	
	
?>
<div class="hero-container <?php foreach ($hero->classes as &$class) echo "hero-".$class." ";//echo all classes  ?>container-fluid nopadding dark">
	
<!--
	
	Hero Priorities
	
	Text < Image < Video < Organism
	
-->
	
	<div class="hero-section">
	
	<?php if(get_field('heroes')): while(has_sub_field('heroes')): ?>
		
			<?php
				
				//Hide After
				if( get_sub_field('hide_after') && ( get_sub_field('hide_after') / 1000 + (24*60*60) ) < date('U') )//if date is set and it has passed
					continue;//Skip
				
				if( $hero->index ) $hero->srcType = "data-lazy";//if it's anything but the first lazy load it
				
				//Setup Image
				$hero->attachment_id = get_sub_field('image');
				
				$hero->link = get_sub_field('link');
				
				$image_attachment = wp_get_attachment_image_src($hero->attachment_id, '720');
				
				$hero->src = $image_attachment[0];
				
				//Setup Foreground
				$hero->text = get_sub_field('title');
				
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