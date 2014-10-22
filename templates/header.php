<header class="banner navbar navbar-default navbar-static-top" role="banner">
  <div class="container">
  
		<div class="collapse navbar-collapse" role="navigation">
			
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
</header>
