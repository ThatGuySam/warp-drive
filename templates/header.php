<?php //debug( $page_options ); ?>
<header class="banner navbar navbar-default navbar-static-top <?php global $page_options; if( isset( $page_options->pagefade ) ) {?> animated fadeIn animated-3s animated-delay-1s<?php } ?>" role="banner">
	<div class="container-fluid primary-nav-container">
	
		<div class="primary-nav" role="navigation">
			
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
  
	<div class="container expanded-nav-container">
		
		<div class="expanded-nav">
			
			<input id="search" name="search" placeholder="Start typing..." type="text" autocomplete="off">
			
			<?php
				if (has_nav_menu('expanded_navigation')) :
				  wp_nav_menu( array(
					'theme_location'	=> 'expanded_navigation', 
					'menu_class'		=> 'nav navbar-nav row'
				  ));
				endif;
			?>
			
		</div>
		
	</div>
</header>
