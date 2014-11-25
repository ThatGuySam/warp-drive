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
			
			
			
			/* Menu */
			
			
			$(".search-toggle").click( function(){
				$(".expanded-nav").toggleClass("expanded-nav-open");
				
				return false;
			});
			
			/* Header */
			
			function sizeHero() {
				
				var $media = $(".hero-background > img");
				
				var wh = window.innerHeight;
				var ww = window.innerWidth;
				
				var ratio = 9/16;
				
				var heroHeight = wh+"px";//-25; 
				var maxHeroHeight = Math.round( ww*ratio )+"px";
				
				if( $("body").hasClass("single-ai1ec_event") ){// if it's an event
					maxHeroHeight = 500+"px";
					heroHeight = "";
				}
				 
				$(".hero-media .hero-section")
					.css("height", heroHeight)
					.css("max-height", maxHeroHeight);
					
				
				$media.each(function() {
					
					if( Math.round( ww*ratio ) > wh ) {
						
						var top_offset = Math.round(
							 (
							 	( ww*ratio ) - wh
							 )/2 
						);
						
						$(this).css("margin-top", -top_offset+"px");
							
					} else {
						
						if( $(this).css("margin-top") ) {
							$(this).css("margin-top", "");
						}
						
					}
					
				});
					
					
			}
			
			
			var $boxes = $('.hero-slick .hero-section');
				
			$boxes.slick({
				arrows: !Modernizr.touch,
				autoplay: true,
				autoplaySpeed: 6000,
				speed: 750,
				fade: !Modernizr.touch,
				easing: 'easeOutQuint'
			});
			
			
			
			/*
			
			Boxes
			
			*/
			
			function boxize($boxesContainer){
					
				var $frame = $boxesContainer.find('.frame'); window.frr = $frame;
				
				$boxesContainer.css("display", "none");
					
				$boxesContainer.css("display", "");
				
				var slidesToShow = $frame.data("show");
				
				//console.log( slidesToShow );
				
				
				$frame.find("ul").slick({
					arrows: !Modernizr.touch,
					infinite: false,
					speed: 750,
					slide: 'li',
					slidesToShow: slidesToShow,
					slidesToScroll: slidesToShow,
					easing: 'easeOutQuint',
					variableWidth: $boxesContainer.hasClass("double-stacked"),
					responsive: [{
						breakpoint: 768,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1,
						}
					}]
				});
			
				// Method calling buttons
				$boxesContainer.on('click', 'button[data-action]', function () {
					var action = $(this).data('action');
			
					switch (action) {
						case 'add':
							//Add slide function
							break;
						case 'remove':
							//Remove slide function
							break;
						default:
							sly[action]();
					}
				});
			}
			
			
			$('.box-boxes').each(function() {
				boxize( $(this) );
				
				if( $(this).hasClass("double-stacked") ){
					//sizeFirstBox( $(this) );
					console.log("Has Double");
				}
				
			});
			
			
			function sizeFirstBox( $boxesContainer ) {
				
				var $firstItem = $boxesContainer.find(".frame ul li:first-child");
				
				var $secondItem = $boxesContainer.find(".frame ul li:nth-child(2)");
				
				var secondItemWidth = $secondItem.outerWidth();
				
				//$firstItem.css('min-width', (secondItemWidth*2)+4+'px' );
				
				console.log( secondItemWidth );
				
				
			}
			
			
			$( window ).resize(function() {
				sizeHero();
			});
			
			
			
			/*
				
			Switch Input
			
			*/
			
			
			var selectorStart = "mode-";
					
			var status = "closed";

			$(".switch-input").on( "click", function() {
				//var smsKey = $(this).data("key");
				//var actionURL = $(this).data("action");
				
				switch(status) {
				  case "closed":
				  	$(this)
				  		.addClass("opened");
				  	$(".switch-input-box").focus();
				  	status = "opened";
				      break;
				  case "opened":
				  	
				  	$(this)
				    	.removeClass("opened");
				    	status = "closed";
				  	break;
				  case "send":
				  	
				  	status = "sending";
				  	
				  	var request = { 
			  			"mode": "send",
			  			"toNumber": $(this).find(".switch-input-box").val().replace(/\D/g,'')
				  	};
				  	
				  	console.log(request);
				  	$(this)
				  		.removeClass("opened")
				  		.addClass("message-"+status);
				  		
					$(".switch-input").submit();
					
					break;
				  default:
			     
				}//switch(mode)
				
				
				$(".switch-input").submit(function( event ) {
					
					event.preventDefault();
				
					var submitUrl = $(this).attr("action");
				
					$.post( submitUrl, $(this).serialize())
						.always(function() {
							$(".switch-input").removeClass("message-"+status);
							status = "sent";
							$(".switch-input").addClass("message-"+status);
							console.log( "Always: "+status );
							setTimeout(function(){
								$(".switch-input")
									.removeClass("valid-number message-"+status);
									status = "closed";
							}, 1000);
						});
					
					
					//send();
					//return false;
					//event.preventDefault();
				});
/*
				$.mask.definitions.e = "[^w+@[a-zA-Z_]+?.[a-zA-Z]{2,3}$]";
				
				$(".switch-input-box")
					.mask("e",{
					placeholder:" ",
					completed:function(){
						$(".switch-input")
					  		.addClass("valid-number");
					  		status = "send";
					}
				});//.mask for .switch-input-box
*/
				
				
				
				$( ".switch-input-box" ).keyup(function() {
					
					var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
					
					if (testEmail.test( $(this).val() )) {
						$(".switch-input")
					  		.addClass("valid-number");
					  		status = "send";
					} else {
						$(".switch-input")
					  		.removeClass("valid-number");
					  		status = "opened";
					}
					
				});
				
			
			});//$(".switch-input").on( "click") 
			
			
			
			
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
					}, 2500, 'easeOutQuint', function() {
						//Unbind Stop for Scrolling
						$("html, body").unbind("scroll mousedown DOMMouseScroll mousewheel keyup");
					});
					
					return false;
				});
				
			});
			
			
			
			
			/* Init */
			
			sizeHero();
			
			//sizeFirstBox();
			
			if( !Modernizr.cssanimations ){
				$("html").addClass("no-cssanimations");
			}
			
			$('.entry-content').selectionSharer();
			
			
/*
			Modernizr.on('webp', function (result) {
				// `result == Modernizr.webp`
				console.log(result);  // either `true` or `false`
				if (result) {
					// Has WebP support
				}
				else {
					// No WebP support
				}
			});
*/
			
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
