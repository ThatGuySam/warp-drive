/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can 
 * always reference jQuery with $, even when in .noConflict() mode.
 *
 * Google CDN, Latest jQuery
 * To use the default WordPress version of jQuery, go to lib/config.php and
 * remove or comment out: add_theme_support('jquery-cdn');
 * ======================================================================== */

(function($) {

// Use this variable to set up the common and page specific functions. If you 
// rename this variable, you will also need to rename the namespace below.
var Roots = {
  // All pages
  common: {
    init: function() {
      // JavaScript to be fired on all pages
      
		if ( undefined !== window.jQuery ) { jQuery(function ($) { 'use strict';
			
			/* easeOutQuint */
			jQuery.extend( jQuery.easing, {
				easeOutQuint: function (x, t, b, c, d) {
					return c*((t=t/d-1)*t*t*t*t + 1) + b;
				}
			});
			
			
			/* Header */
			
			function sizeHero() {
				
				var $media = $(".hero-organism > img");
				
				var wh = window.innerHeight;
				var ww = window.innerWidth;
				
				var heroHeight = wh-25;
				
				var ratio = 9/16;
				
				$(".hero-media .hero-section")
					.css("height", heroHeight+"px")
					.css("max-height", Math.round( ww*ratio )+"px");
					
					
				if( ( ww*ratio ) > heroHeight ) {
					
					var top_offset = Math.round(
						 (
						 	( ww*ratio ) - heroHeight 
						 )/2 
					);
					
					$media.css("margin-top", -top_offset+"px");
						
				} else {
					
					if( $media.css("margin-top") ) {
						$media.css("margin-top", "");
					}
					
				}
					
/*
				$(".foreground")
					.css("width", Math.round( wh/ratio )+"px");
*/
					
					
			}
			
			$( window ).resize(function() {
				sizeHero();
			});
			
			
			$('.hero-slick .hero-section').slick({
				arrows: !Modernizr.touch,
				autoplay: true,
				autoplaySpeed: 6000,
				speed: 1000,
				fade: true,
				speed: 750,
				fade: !Modernizr.touch,
				easing: 'easeOutQuint'
			});
			
			
			
			
			/* Content */
			
			$("a.scrollto").each( function() {
			
				var $this = $(this);
				
				$this.click(function() {
					
					//Bind Stop for Scrolling
					$("html, body").bind("scroll mousedown DOMMouseScroll mousewheel keyup", function(){
						$('html, body').stop();
					});
					
					$('html, body').animate({
						scrollTop: $( $this.attr('href') ).offset().top
					}, 750, "easeOutQuint");
					}, 2500, 'easeOutQuint', function() {
						//Unbind Stop for Scrolling
						$("html, body").unbind("scroll mousedown DOMMouseScroll mousewheel keyup");
					});
					
					event.preventDefault();
				});
				
			});
			
			
			
			
			/* Init */
			
			sizeHero();
			
		}); }
		
    }
  },
  // Home page
  home: {
    init: function() {
      // JavaScript to be fired on the home page
    }
  },
  // About us page, note the change from about-us to about_us.
  about_us: {
    init: function() {
      // JavaScript to be fired on the about us page
    }
  }
};

// The routing fires all common scripts, followed by the page specific scripts.
// Add additional events for more control over timing e.g. a finalize event
var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = Roots;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {
    UTIL.fire('common');

    $.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });
  }
};

$(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
