<?php
	
	$hero = new stdClass();
	
	$hero->kind = "text";
	
	if( has_post_thumbnail() ){
		$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "full" );
		
		$hero->kind = "image";
		
		$hero->src = $thumbnail_src[0];
	}
	
	
?>
<div class="hero-container hero-<?php echo $hero->kind; ?> container-fluid nopadding dark">
	
<!--
	
	Hero Priorities
	
	Text < Image < Video < Slider < Organism
	
-->
	
	<div class="hero-section">
		<div class="hero-organism">
		
			<?php
				
				switch ($hero->kind) {
				  case "image":
				    ob_start(); ?>
				    
				    	<img src="<?php echo $hero->src; ?>" alt="<?php echo roots_title(); ?> Image">
				    
				    <?php $hero->output = ob_get_clean();
				    break;
				  case "video":
				    //code to be executed if n=label2;
				    break;
				  case "slider":
				    //code to be executed if n=label3;
				    break;
				  default:
				    //"";
				}
				
			?>
					
					<?php echo $hero->output; ?>
				
			
			<div class="container">
				<div class="page-header">
					<h1>
					<?php echo roots_title(); ?>
					</h1>
					<div class="read-more">
						<a href="#content" class="scrollto">
							<span>More</span>
							<br>
							<span><i class="fa fa-angle-down fa-2x"></i></span>
						</a>
					</div>
				</div>
			</div>
			
		</div>
	</div>

	
	
	
</div>