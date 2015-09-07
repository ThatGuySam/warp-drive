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
 * ======================================================================== */

(function($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages
        
        
        if ( undefined !== window.jQuery ) { jQuery(function ($) { 'use strict';
			
			/* easeOutQuint */
			$.extend( jQuery.easing, {
				easeOutQuint: function (x, t, b, c, d) {
					return c*((t=t/d-1)*t*t*t*t + 1) + b;
				}
			});
			
			
			/* Generic Functions */
			
			
			//Google Translate
			function googleTranslateElementInit() {
				new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.FloatPosition.TOP_LEFT, gaTrack: true, gaId: 'UA-9224686-1'}, 'google_translate_element');
			}
			
			//UX Friendly scroll
			gc.scrollTo = function ( $here ) {
				
				console.log( $here );
				
				//Stop Immediatelly for Scrolling
				$("html, body").bind("scroll mousedown DOMMouseScroll mousewheel keyup", function(){
					$('html, body').stop();
					$("body").removeClass("disable-hover");
				});
				
				
				$("body").addClass("disable-hover");//disable hovering for 60fps scolling
				
				$('html, body').animate({
					scrollTop: $here
				
				}, 2500, 'easeOutQuint', function() {
					//Unbind Stop for Scrolling
					$("html, body").unbind("scroll mousedown DOMMouseScroll mousewheel keyup");
					$("body").removeClass("disable-hover");
				});
				
				return false;
			};
			
			
			
			/* Menu */
			
			function toggleExMenu( action ) { 
				
				var actions = ['close','open'];
				var closed = $("html").hasClass("expanded-nav-open") ? 0 : 1;
				var exMenuHeight = $('#menu-expanded-navigation').outerHeight();
				var toggleSelector = '.search-toggle a span:visible';
				
				if(typeof(action)==='undefined'){ action = actions[closed]; }
				
				//$(".expanded-nav, html").toggleClass("expanded-nav-open");
				
				switch (action) {
					case 'open':
						
						$("html").addClass("expanded-nav-open");
						$(".expanded-nav").css( 'height' , exMenuHeight+'px' );
						
						//$(".search-toggle i").removeClass("gc-search").addClass("gc-cancel");
						window.menuClosedText = $(toggleSelector).text();
						$(toggleSelector).text("Close");
						
						console.log( window.menuClosedText );
						
						if(!Modernizr.touch) { $("#search").focus(); }
						
						console.log( "Opening Menu!" );
						
						break;
					case 'close':
						
						$("html").removeClass("expanded-nav-open");
						$(".expanded-nav").css( 'height' , '' );
						$(toggleSelector).text(window.menuClosedText);
						//$(".search-toggle i").removeClass("gc-cancel").addClass("gc-search");
						
						console.log( "Closing Menu!" );
						
						break;
				}
				
			}
			
			// Takes the gutter width from the bottom margin of .post
			var menuFirstItem =		'#menu-expanded-navigation > li.menu-item-first';
			var menuItemSelector =	'#menu-expanded-navigation > li';
			var menuGutter =		parseInt($(menuItemSelector).css('marginBottom'));
			var menuContainer =		$('#menu-expanded-navigation');
		 
		 
		 
			// Creates an instance of Masonry on #posts
		 
/*
			var $exMenMason = menuContainer.masonry({
				gutter: menuGutter,
				itemSelector: menuItemSelector,
				columnWidth: menuFirstItem
			});
*/
			
/*
			$exMenMason.on( 'layoutComplete', function() {
				
			});
*/
			
			
			$(".search-toggle").click( function(){
				
				toggleExMenu(); 
				
				return false;
			});
			
			$('#search').hideseek({
				list: '.sub-menu, .ex-menu-item',
				navigation: true
			});
			
			//On search complete
			$('#search').on("_after", function() {
				
				//$exMenMason.masonry();
				
				var exMenuHeight = $('#menu-expanded-navigation').outerHeight();
				$(".expanded-nav").css( 'height' , exMenuHeight+50+'px' );
				
				
				
				console.log( $('#menu-expanded-navigation > li a:visible') );
				
				if($('#menu-expanded-navigation > li a:visible').length === 0) {
					console.log("No mo");
				}
				
			});
			
/*
			$( "#search" ).keyup(function() {
				
				if($('#menu-expanded-navigation').children(':visible').length === 0) {
					// action when all are hidden
				}
				
			});
*/
			
			
			// This code fires every time a user resizes the screen and only affects .post elements
			// whose parent class isn't .container. Triggers resize first so nothing looks weird.
			
			
			/* Header */
			
			var wh;
			var ww;
			
			$(window).on('resize', function() {
				
				var $media = $(".hero-background > img");
				var $section = $(".hero-media .hero-section");
				var sectionHeight = $section.outerHeight();
				
				wh = window.innerHeight;
				ww = window.innerWidth;
				
				var ratio = 9/16;
				
				var heroHeight = wh+"px";//-25; 
				var maxHeroHeight = Math.round( ww*ratio )+"px";
				
				if( $("body").hasClass("single-ai1ec_event") ){// if it's an event
					maxHeroHeight = 500+"px";
					heroHeight = "";
				}
				
				if( $(".page-header .hero-content").text().length > 0 ){
					heroHeight = "";
					if( ww < 900 ) {
						maxHeroHeight = "";
					}
				}
				 
				$section
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
				
				
				//Expanded Nav Masonry
				if (!$('#menu-expanded-navigation').parent().hasClass('container')) {
					
					// Resets all widths to 'auto' to sterilize calculations
					var post_width = $(menuFirstItem).width() + menuGutter;
					$('#menu-expanded-navigation, .expanded-nav').css('width', 'auto');
					
					
					
					// Calculates how many .post elements will actually fit per row. Could this code be cleaner?
					var posts_per_row = $('#menu-expanded-navigation').innerWidth() / post_width;
					var floor_posts_width = (Math.floor(posts_per_row) * post_width) - menuGutter;
					var ceil_posts_width = (Math.ceil(posts_per_row) * post_width) - menuGutter;
					var posts_width = (ceil_posts_width > $('#menu-expanded-navigation').innerWidth()) ? floor_posts_width : ceil_posts_width;
					if (posts_width === $(menuItemSelector).width()) {
						posts_width = '100%';
					}
					
					
					
					// Ensures that all top-level elements have equal width and stay centered
					$('#menu-expanded-navigation, .expanded-nav').css('width', posts_width);
					$('.expanded-nav').css({'margin': '0 auto'});
		        
				}
					
					
			}).trigger('resize');
			
			
			
			/*
			
			Boxes
			
			*/
			
			
			//Hero Slickize
			var $heroes = $('.hero-slick .hero-section');
				
			$heroes.slick({
				arrows: !Modernizr.touch,
				autoplay: true,
				accessibility: true,
				autoplaySpeed: 8000,
				speed: 750,
				fade: !Modernizr.touch,
				easing: 'easeOutQuint'
			});
			
			
			//Slider Boxes
			function slickize($boxesContainer){
					
				var $frame = $boxesContainer.find('.frame'); window.frr = $frame;
				
				var slidesToShow = $frame.data("show");
				
				$frame.find("ul").slick({
					arrows: !Modernizr.touch,
					//accessibility: false,
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
			}
			
			
			//Masonry Boxes
			function masonize($boxesContainer){
				
				// Takes the gutter width from the bottom margin
				var $frame = $boxesContainer.find('.frame'); window.frr = $frame;
				var $list = $boxesContainer.find('.frame ul');
				var $firstItem =		$list.find("li:first-child");
				var $itemSelector =	$list.find("li");
				var gutter =		parseInt($itemSelector.css('marginBottom'));
				var slidesToShow = $frame.data("show");

				
				// Creates an instance of Masonry
/*
				var $boxesMason = $list.masonry({
					gutter: gutter,
					itemSelector: 'li',
					columnWidth: 'li:first-child'
				});
*/
				
				var frameHeight = $list.outerHeight(true);
				var frameTop = $list.offset().top;
				var frameBottom = frameTop+frameHeight;
				var scrollBottom = $(window).scrollTop() + wh;
				var $nextRow = $list.find(".box-lazyload").slice(0,slidesToShow);
				var scrollEvents = 'scroll DOMMouseScroll';
				var scrollTimer;
				
				//Relayout Listener
/*
				$boxesMason.on( 'layoutComplete', function() {
					
				});
*/
				
/*
				$list.masonry( 'on', 'layoutComplete', function( msnryInstance, laidOutItems ) {
					frameHeight = $list.outerHeight(true);
					frameTop = $list.offset().top;
					frameBottom = frameTop+frameHeight;
					
					if( $nextRow.length ) {
						
						clearTimeout(scrollTimer);
					    scrollTimer = setTimeout(function() {
					        
					        loadBoxes();
					        
					    }, 100);
					    
					}
				});
*/
				
				
				function loadBoxes() {
					
					scrollBottom = $(window).scrollTop() + wh;
					
			        if( scrollBottom >= frameBottom + 100 ) {
				        
			            $nextRow = $list.find(".box-lazyload").slice(0,slidesToShow);
			            
			            $nextRow.each(function( i ){
				            var $box = $(this);
				            var $image = $(this).find('.box-image img');
				            var delay = i*100;
				            
				            $box.css('transition-delay', delay+'ms');//Staggering
				            
				            $image.unveil(0, function() {
								$(this).load(function() {
									$box.removeClass("box-lazyload");
									//if( i === $nextRow.length-1 ){
										//$list.masonry();
									//}
								});
							});
			            });
			            
			        }
			        
			        return false;
			        
			        //$itemSelector.find(".box-lazyload").slice(0,2)
			    }
				
				$(window).on( scrollEvents, function(event) {
					clearTimeout(scrollTimer);
				    scrollTimer = setTimeout(function() {
				        
				        loadBoxes(event);
				        
				    }, 100);
				    event.stopPropagation();
				}).trigger('scroll');
				
				//var slidesToShow = $list.data("show");
			}
			
			//Let's make som slides
			$('.box-boxes').each(function() {
				
				//$boxesContainer
				var $this = $(this);
				
				if( $this.hasClass('boxes-slick') ){
					slickize( $this );
				} else if( $this.hasClass('boxes-masonry') ){
					masonize( $this );
				}
				
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
			
			
			
			$('.countdown').each( function(){
				
				var until = $(this).data("until");
				
				$(this).countdown( until )
					.on('update.countdown', function(event) {
						
						var timeUnits = [
							['d','day','day'],
							['H','hour','hr'],
							['M','minute','min'],
						];
						
						var count_text = 'Live in ';
						
						for (var i=0; i < timeUnits.length; ++i) {//Add timeUnits if they aren't 0
							
							var shorthand =	timeUnits[i][2];
							var unit =		timeUnits[i][1];
							var u =			timeUnits[i][0];
							
							if( event.offset[(unit+"s")] ){
								count_text += '<span>%-'+u+'</span> <small>'+shorthand+'%!'+u+'</small> ';
							}
								
						}
						
						
						count_text += '<span>%-S</span> <small>sec%!S</small> ';//Add Seconds
						
						var $this = $(this).html(event.strftime( count_text ));
					})
					.on('finish.countdown', function(event) {
						
						console.log( "Finished" );
						
						$("html").addClass("is-live");
						
						var $this = $(this).html( '<span class="live">Live Now</span>' );
						
					});
				
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
					
				});
				
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
			
			$("a.scrollto, .scrollto > a").each(function(){
				
				var selector = $(this).attr('href');
				
				$(this).click(function(){
					
					gc.scrollTo( $( selector ).offset().top );
					
					return false;
				});
				
			});
			
			//$('.entry-content').selectionSharer();
			
			
			
			
			/* Init */
			
			$.material.init();
			
			//sizeFirstBox();
			
			if( !Modernizr.cssanimations ){
				$("html").addClass("no-cssanimations");
			}
			
			//$("#hero .hero-section").click();
			
			
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
		
		//end
		
		
		
      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS
      }
    },
    // About us page, note the change from about-us to about_us.
    'about_us': {
      init: function() {
        // JavaScript to be fired on the about us page
      }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
