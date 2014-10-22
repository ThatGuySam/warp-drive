<header class="banner navbar navbar-default navbar-static-top" role="banner">
  <div class="container">
  
  	<div class="row">
      	<div class="col-sm-1">
	      	
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
		      <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>"><i class="icon-guts-g"></i></a>
		    </div>
		    
      	</div>
      	
      	
		<div class="collapse navbar-collapse row" role="navigation">
			<div class="col-sm-10">
			
			<?php
				if (has_nav_menu('primary_navigation')) :
				  wp_nav_menu( array(
					'theme_location'	=> 'primary_navigation', 
					'menu_class'		=> 'nav navbar-nav row'
				  ));
				endif;
			?>
			
			</div>
		</div>

  	</div>
  </div>
</header>
