<?php
	
	$hero = new stdClass();
	
	$hero->classes = array();
	
	$hero->kind = "text";
	
	if( has_post_thumbnail() ){
		$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "full" );
		
		$hero->kind = "media";
		
		$hero->media = "image";
		
		$hero->src = $thumbnail_src[0];
	}
	
	
	array_push( $hero->classes, $hero->kind );
	
	
	if(get_field('heroes')) array_push( $hero->classes, "slick" );
	
	
?>
<div class="hero-container <?php foreach ($hero->classes as &$class) echo "hero-".$class." "  ?> container-fluid nopadding dark">
	
<!--
	
	Hero Priorities
	
	Text < Slider < Image < Video < Organism
	
-->
	
	<div class="hero-section">
	
	<?php if(get_field('heroes')): while(has_sub_field('heroes')): ?>
		
			<?php
				
				
				//Setup Image
				$attachment_id = get_sub_field('image');
				
				$image_attachment = wp_get_attachment_image_src($attachment_id, '720');
				
				$hero->src = $image_attachment[0];
				
			?>
			
			
			<?php echo heroOrganism($hero); ?>
			
		
		<?php endwhile; 
		
		else: ?>
		
			<?php echo heroOrganism($hero); ?>
		
		<?php endif; ?>
		
	</div>

	
	
	
</div>