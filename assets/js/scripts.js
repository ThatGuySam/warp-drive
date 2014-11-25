/*
     _ _      _       _
 ___| (_) ___| | __  (_)___
/ __| | |/ __| |/ /  | / __|
\__ \ | | (__|   < _ | \__ \
|___/_|_|\___|_|\_(_)/ |___/
                   |__/

 Version: 1.3.15
  Author: Ken Wheeler
 Website: http://kenwheeler.github.io
    Docs: http://kenwheeler.github.io/slick
    Repo: http://github.com/kenwheeler/slick
  Issues: http://github.com/kenwheeler/slick/issues

 */

!function(a){"use strict";"function"==typeof define&&define.amd?define(["jquery"],a):"undefined"!=typeof exports?module.exports=a(require("jquery")):a(jQuery)}(function(a){"use strict";var b=window.Slick||{};b=function(){function c(c,d){var f,g,e=this;if(e.defaults={accessibility:!0,adaptiveHeight:!1,appendArrows:a(c),appendDots:a(c),arrows:!0,asNavFor:null,prevArrow:'<button type="button" data-role="none" class="slick-prev">Previous</button>',nextArrow:'<button type="button" data-role="none" class="slick-next">Next</button>',autoplay:!1,autoplaySpeed:3e3,centerMode:!1,centerPadding:"50px",cssEase:"ease",customPaging:function(a,b){return'<button type="button" data-role="none">'+(b+1)+"</button>"},dots:!1,dotsClass:"slick-dots",draggable:!0,easing:"linear",fade:!1,focusOnSelect:!1,infinite:!0,initialSlide:0,lazyLoad:"ondemand",onBeforeChange:null,onAfterChange:null,onInit:null,onReInit:null,onSetPosition:null,pauseOnHover:!0,pauseOnDotsHover:!1,respondTo:"window",responsive:null,rtl:!1,slide:"div",slidesToShow:1,slidesToScroll:1,speed:500,swipe:!0,swipeToSlide:!1,touchMove:!0,touchThreshold:5,useCSS:!0,variableWidth:!1,vertical:!1,waitForAnimate:!0},e.initials={animating:!1,dragging:!1,autoPlayTimer:null,currentDirection:0,currentLeft:null,currentSlide:0,direction:1,$dots:null,listWidth:null,listHeight:null,loadIndex:0,$nextArrow:null,$prevArrow:null,slideCount:null,slideWidth:null,$slideTrack:null,$slides:null,sliding:!1,slideOffset:0,swipeLeft:null,$list:null,touchObject:{},transformsEnabled:!1},a.extend(e,e.initials),e.activeBreakpoint=null,e.animType=null,e.animProp=null,e.breakpoints=[],e.breakpointSettings=[],e.cssTransitions=!1,e.paused=!1,e.positionProp=null,e.respondTo=null,e.shouldClick=!0,e.$slider=a(c),e.$slidesCache=null,e.transformType=null,e.transitionType=null,e.windowWidth=0,e.windowTimer=null,e.options=a.extend({},e.defaults,d),e.currentSlide=e.options.initialSlide,e.originalSettings=e.options,f=e.options.responsive||null,f&&f.length>-1){e.respondTo=e.options.respondTo||"window";for(g in f)f.hasOwnProperty(g)&&(e.breakpoints.push(f[g].breakpoint),e.breakpointSettings[f[g].breakpoint]=f[g].settings);e.breakpoints.sort(function(a,b){return b-a})}e.autoPlay=a.proxy(e.autoPlay,e),e.autoPlayClear=a.proxy(e.autoPlayClear,e),e.changeSlide=a.proxy(e.changeSlide,e),e.clickHandler=a.proxy(e.clickHandler,e),e.selectHandler=a.proxy(e.selectHandler,e),e.setPosition=a.proxy(e.setPosition,e),e.swipeHandler=a.proxy(e.swipeHandler,e),e.dragHandler=a.proxy(e.dragHandler,e),e.keyHandler=a.proxy(e.keyHandler,e),e.autoPlayIterator=a.proxy(e.autoPlayIterator,e),e.instanceUid=b++,e.htmlExpr=/^(?:\s*(<[\w\W]+>)[^>]*)$/,e.init(),e.checkResponsive()}var b=0;return c}(),b.prototype.addSlide=function(b,c,d){var e=this;if("boolean"==typeof c)d=c,c=null;else if(0>c||c>=e.slideCount)return!1;e.unload(),"number"==typeof c?0===c&&0===e.$slides.length?a(b).appendTo(e.$slideTrack):d?a(b).insertBefore(e.$slides.eq(c)):a(b).insertAfter(e.$slides.eq(c)):d===!0?a(b).prependTo(e.$slideTrack):a(b).appendTo(e.$slideTrack),e.$slides=e.$slideTrack.children(this.options.slide),e.$slideTrack.children(this.options.slide).detach(),e.$slideTrack.append(e.$slides),e.$slides.each(function(b,c){a(c).attr("index",b)}),e.$slidesCache=e.$slides,e.reinit()},b.prototype.animateSlide=function(b,c){var d={},e=this;if(1===e.options.slidesToShow&&e.options.adaptiveHeight===!0&&e.options.vertical===!1){var f=e.$slides.eq(e.currentSlide).outerHeight(!0);e.$list.animate({height:f},e.options.speed)}e.options.rtl===!0&&e.options.vertical===!1&&(b=-b),e.transformsEnabled===!1?e.options.vertical===!1?e.$slideTrack.animate({left:b},e.options.speed,e.options.easing,c):e.$slideTrack.animate({top:b},e.options.speed,e.options.easing,c):e.cssTransitions===!1?a({animStart:e.currentLeft}).animate({animStart:b},{duration:e.options.speed,easing:e.options.easing,step:function(a){e.options.vertical===!1?(d[e.animType]="translate("+a+"px, 0px)",e.$slideTrack.css(d)):(d[e.animType]="translate(0px,"+a+"px)",e.$slideTrack.css(d))},complete:function(){c&&c.call()}}):(e.applyTransition(),d[e.animType]=e.options.vertical===!1?"translate3d("+b+"px, 0px, 0px)":"translate3d(0px,"+b+"px, 0px)",e.$slideTrack.css(d),c&&setTimeout(function(){e.disableTransition(),c.call()},e.options.speed))},b.prototype.asNavFor=function(b){var c=this,d=null!=c.options.asNavFor?a(c.options.asNavFor).getSlick():null;null!=d&&d.slideHandler(b,!0)},b.prototype.applyTransition=function(a){var b=this,c={};c[b.transitionType]=b.options.fade===!1?b.transformType+" "+b.options.speed+"ms "+b.options.cssEase:"opacity "+b.options.speed+"ms "+b.options.cssEase,b.options.fade===!1?b.$slideTrack.css(c):b.$slides.eq(a).css(c)},b.prototype.autoPlay=function(){var a=this;a.autoPlayTimer&&clearInterval(a.autoPlayTimer),a.slideCount>a.options.slidesToShow&&a.paused!==!0&&(a.autoPlayTimer=setInterval(a.autoPlayIterator,a.options.autoplaySpeed))},b.prototype.autoPlayClear=function(){var a=this;a.autoPlayTimer&&clearInterval(a.autoPlayTimer)},b.prototype.autoPlayIterator=function(){var a=this;a.options.infinite===!1?1===a.direction?(a.currentSlide+1===a.slideCount-1&&(a.direction=0),a.slideHandler(a.currentSlide+a.options.slidesToScroll)):(0===a.currentSlide-1&&(a.direction=1),a.slideHandler(a.currentSlide-a.options.slidesToScroll)):a.slideHandler(a.currentSlide+a.options.slidesToScroll)},b.prototype.buildArrows=function(){var b=this;b.options.arrows===!0&&b.slideCount>b.options.slidesToShow&&(b.$prevArrow=a(b.options.prevArrow),b.$nextArrow=a(b.options.nextArrow),b.htmlExpr.test(b.options.prevArrow)&&b.$prevArrow.appendTo(b.options.appendArrows),b.htmlExpr.test(b.options.nextArrow)&&b.$nextArrow.appendTo(b.options.appendArrows),b.options.infinite!==!0&&b.$prevArrow.addClass("slick-disabled"))},b.prototype.buildDots=function(){var c,d,b=this;if(b.options.dots===!0&&b.slideCount>b.options.slidesToShow){for(d='<ul class="'+b.options.dotsClass+'">',c=0;c<=b.getDotCount();c+=1)d+="<li>"+b.options.customPaging.call(this,b,c)+"</li>";d+="</ul>",b.$dots=a(d).appendTo(b.options.appendDots),b.$dots.find("li").first().addClass("slick-active")}},b.prototype.buildOut=function(){var b=this;b.$slides=b.$slider.children(b.options.slide+":not(.slick-cloned)").addClass("slick-slide"),b.slideCount=b.$slides.length,b.$slides.each(function(b,c){a(c).attr("index",b)}),b.$slidesCache=b.$slides,b.$slider.addClass("slick-slider"),b.$slideTrack=0===b.slideCount?a('<div class="slick-track"/>').appendTo(b.$slider):b.$slides.wrapAll('<div class="slick-track"/>').parent(),b.$list=b.$slideTrack.wrap('<div class="slick-list"/>').parent(),b.$slideTrack.css("opacity",0),b.options.centerMode===!0&&(b.options.slidesToScroll=1),a("img[data-lazy]",b.$slider).not("[src]").addClass("slick-loading"),b.setupInfinite(),b.buildArrows(),b.buildDots(),b.updateDots(),b.options.accessibility===!0&&b.$list.prop("tabIndex",0),b.setSlideClasses("number"==typeof this.currentSlide?this.currentSlide:0),b.options.draggable===!0&&b.$list.addClass("draggable")},b.prototype.checkResponsive=function(){var c,d,e,b=this,f=b.$slider.width(),g=window.innerWidth||a(window).width();if("window"===b.respondTo?e=g:"slider"===b.respondTo?e=f:"min"===b.respondTo&&(e=Math.min(g,f)),b.originalSettings.responsive&&b.originalSettings.responsive.length>-1&&null!==b.originalSettings.responsive){d=null;for(c in b.breakpoints)b.breakpoints.hasOwnProperty(c)&&e<b.breakpoints[c]&&(d=b.breakpoints[c]);null!==d?null!==b.activeBreakpoint?d!==b.activeBreakpoint&&(b.activeBreakpoint=d,b.options=a.extend({},b.originalSettings,b.breakpointSettings[d]),b.refresh()):(b.activeBreakpoint=d,b.options=a.extend({},b.originalSettings,b.breakpointSettings[d]),b.refresh()):null!==b.activeBreakpoint&&(b.activeBreakpoint=null,b.options=b.originalSettings,b.refresh())}},b.prototype.changeSlide=function(b,c){var f,g,h,i,j,d=this,e=a(b.target);switch(e.is("a")&&b.preventDefault(),h=0!==d.slideCount%d.options.slidesToScroll,f=h?0:(d.slideCount-d.currentSlide)%d.options.slidesToScroll,b.data.message){case"previous":g=0===f?d.options.slidesToScroll:d.options.slidesToShow-f,d.slideCount>d.options.slidesToShow&&d.slideHandler(d.currentSlide-g,!1,c);break;case"next":g=0===f?d.options.slidesToScroll:f,d.slideCount>d.options.slidesToShow&&d.slideHandler(d.currentSlide+g,!1,c);break;case"index":var k=0===b.data.index?0:b.data.index||a(b.target).parent().index()*d.options.slidesToScroll;if(i=d.getNavigableIndexes(),j=0,i[k]&&i[k]===k)if(k>i[i.length-1])k=i[i.length-1];else for(var l in i){if(k<i[l]){k=j;break}j=i[l]}d.slideHandler(k,!1,c);default:return}},b.prototype.clickHandler=function(a){var b=this;b.shouldClick===!1&&(a.stopImmediatePropagation(),a.stopPropagation(),a.preventDefault())},b.prototype.destroy=function(){var b=this;b.autoPlayClear(),b.touchObject={},a(".slick-cloned",b.$slider).remove(),b.$dots&&b.$dots.remove(),b.$prevArrow&&"object"!=typeof b.options.prevArrow&&b.$prevArrow.remove(),b.$nextArrow&&"object"!=typeof b.options.nextArrow&&b.$nextArrow.remove(),b.$slides.parent().hasClass("slick-track")&&b.$slides.unwrap().unwrap(),b.$slides.removeClass("slick-slide slick-active slick-center slick-visible").removeAttr("index").css({position:"",left:"",top:"",zIndex:"",opacity:"",width:""}),b.$slider.removeClass("slick-slider"),b.$slider.removeClass("slick-initialized"),b.$list.off(".slick"),a(window).off(".slick-"+b.instanceUid),a(document).off(".slick-"+b.instanceUid)},b.prototype.disableTransition=function(a){var b=this,c={};c[b.transitionType]="",b.options.fade===!1?b.$slideTrack.css(c):b.$slides.eq(a).css(c)},b.prototype.fadeSlide=function(a,b,c){var d=this;d.cssTransitions===!1?(d.$slides.eq(b).css({zIndex:1e3}),d.$slides.eq(b).animate({opacity:1},d.options.speed,d.options.easing,c),d.$slides.eq(a).animate({opacity:0},d.options.speed,d.options.easing)):(d.applyTransition(b),d.applyTransition(a),d.$slides.eq(b).css({opacity:1,zIndex:1e3}),d.$slides.eq(a).css({opacity:0}),c&&setTimeout(function(){d.disableTransition(b),d.disableTransition(a),c.call()},d.options.speed))},b.prototype.filterSlides=function(a){var b=this;null!==a&&(b.unload(),b.$slideTrack.children(this.options.slide).detach(),b.$slidesCache.filter(a).appendTo(b.$slideTrack),b.reinit())},b.prototype.getCurrent=function(){var a=this;return a.currentSlide},b.prototype.getDotCount=function(){var a=this,b=0,c=0,d=0;if(a.options.infinite===!0)d=Math.ceil(a.slideCount/a.options.slidesToScroll);else for(;b<a.slideCount;)++d,b=c+a.options.slidesToShow,c+=a.options.slidesToScroll<=a.options.slidesToShow?a.options.slidesToScroll:a.options.slidesToShow;return d-1},b.prototype.getLeft=function(a){var c,d,g,b=this,e=0;return b.slideOffset=0,d=b.$slides.first().outerHeight(),b.options.infinite===!0?(b.slideCount>b.options.slidesToShow&&(b.slideOffset=-1*b.slideWidth*b.options.slidesToShow,e=-1*d*b.options.slidesToShow),0!==b.slideCount%b.options.slidesToScroll&&a+b.options.slidesToScroll>b.slideCount&&b.slideCount>b.options.slidesToShow&&(a>b.slideCount?(b.slideOffset=-1*(b.options.slidesToShow-(a-b.slideCount))*b.slideWidth,e=-1*(b.options.slidesToShow-(a-b.slideCount))*d):(b.slideOffset=-1*b.slideCount%b.options.slidesToScroll*b.slideWidth,e=-1*b.slideCount%b.options.slidesToScroll*d))):a+b.options.slidesToShow>b.slideCount&&(b.slideOffset=(a+b.options.slidesToShow-b.slideCount)*b.slideWidth,e=(a+b.options.slidesToShow-b.slideCount)*d),b.slideCount<=b.options.slidesToShow&&(b.slideOffset=0,e=0),b.options.centerMode===!0&&b.options.infinite===!0?b.slideOffset+=b.slideWidth*Math.floor(b.options.slidesToShow/2)-b.slideWidth:b.options.centerMode===!0&&(b.slideOffset=0,b.slideOffset+=b.slideWidth*Math.floor(b.options.slidesToShow/2)),c=b.options.vertical===!1?-1*a*b.slideWidth+b.slideOffset:-1*a*d+e,b.options.variableWidth===!0&&(g=b.slideCount<=b.options.slidesToShow||b.options.infinite===!1?b.$slideTrack.children(".slick-slide").eq(a):b.$slideTrack.children(".slick-slide").eq(a+b.options.slidesToShow),c=g[0]?-1*g[0].offsetLeft:0,b.options.centerMode===!0&&(g=b.options.infinite===!1?b.$slideTrack.children(".slick-slide").eq(a):b.$slideTrack.children(".slick-slide").eq(a+b.options.slidesToShow+1),c=g[0]?-1*g[0].offsetLeft:0,c+=(b.$list.width()-g.outerWidth())/2)),c},b.prototype.getNavigableIndexes=function(){for(var a=this,b=0,c=0,d=[];b<a.slideCount;)d.push(b),b=c+a.options.slidesToScroll,c+=a.options.slidesToScroll<=a.options.slidesToShow?a.options.slidesToScroll:a.options.slidesToShow;return d},b.prototype.getSlideCount=function(){var c,b=this;if(b.options.swipeToSlide===!0){var d=null;return b.$slideTrack.find(".slick-slide").each(function(c,e){return e.offsetLeft+a(e).outerWidth()/2>-1*b.swipeLeft?(d=e,!1):void 0}),c=Math.abs(a(d).attr("index")-b.currentSlide)}return b.options.slidesToScroll},b.prototype.init=function(){var b=this;a(b.$slider).hasClass("slick-initialized")||(a(b.$slider).addClass("slick-initialized"),b.buildOut(),b.setProps(),b.startLoad(),b.loadSlider(),b.initializeEvents(),b.updateArrows(),b.updateDots()),null!==b.options.onInit&&b.options.onInit.call(this,b)},b.prototype.initArrowEvents=function(){var a=this;a.options.arrows===!0&&a.slideCount>a.options.slidesToShow&&(a.$prevArrow.on("click.slick",{message:"previous"},a.changeSlide),a.$nextArrow.on("click.slick",{message:"next"},a.changeSlide))},b.prototype.initDotEvents=function(){var b=this;b.options.dots===!0&&b.slideCount>b.options.slidesToShow&&a("li",b.$dots).on("click.slick",{message:"index"},b.changeSlide),b.options.dots===!0&&b.options.pauseOnDotsHover===!0&&b.options.autoplay===!0&&a("li",b.$dots).on("mouseenter.slick",function(){b.paused=!0,b.autoPlayClear()}).on("mouseleave.slick",function(){b.paused=!1,b.autoPlay()})},b.prototype.initializeEvents=function(){var b=this;b.initArrowEvents(),b.initDotEvents(),b.$list.on("touchstart.slick mousedown.slick",{action:"start"},b.swipeHandler),b.$list.on("touchmove.slick mousemove.slick",{action:"move"},b.swipeHandler),b.$list.on("touchend.slick mouseup.slick",{action:"end"},b.swipeHandler),b.$list.on("touchcancel.slick mouseleave.slick",{action:"end"},b.swipeHandler),b.$list.on("click.slick",b.clickHandler),b.options.pauseOnHover===!0&&b.options.autoplay===!0&&(b.$list.on("mouseenter.slick",function(){b.paused=!0,b.autoPlayClear()}),b.$list.on("mouseleave.slick",function(){b.paused=!1,b.autoPlay()})),b.options.accessibility===!0&&b.$list.on("keydown.slick",b.keyHandler),b.options.focusOnSelect===!0&&a(b.options.slide,b.$slideTrack).on("click.slick",b.selectHandler),a(window).on("orientationchange.slick.slick-"+b.instanceUid,function(){b.checkResponsive(),b.setPosition()}),a(window).on("resize.slick.slick-"+b.instanceUid,function(){a(window).width()!==b.windowWidth&&(clearTimeout(b.windowDelay),b.windowDelay=window.setTimeout(function(){b.windowWidth=a(window).width(),b.checkResponsive(),b.setPosition()},50))}),a("*[draggable!=true]",b.$slideTrack).on("dragstart",function(a){a.preventDefault()}),a(window).on("load.slick.slick-"+b.instanceUid,b.setPosition),a(document).on("ready.slick.slick-"+b.instanceUid,b.setPosition)},b.prototype.initUI=function(){var a=this;a.options.arrows===!0&&a.slideCount>a.options.slidesToShow&&(a.$prevArrow.show(),a.$nextArrow.show()),a.options.dots===!0&&a.slideCount>a.options.slidesToShow&&a.$dots.show(),a.options.autoplay===!0&&a.autoPlay()},b.prototype.keyHandler=function(a){var b=this;37===a.keyCode&&b.options.accessibility===!0?b.changeSlide({data:{message:"previous"}}):39===a.keyCode&&b.options.accessibility===!0&&b.changeSlide({data:{message:"next"}})},b.prototype.lazyLoad=function(){function g(b){a("img[data-lazy]",b).each(function(){var b=a(this),c=a(this).attr("data-lazy");b.load(function(){b.animate({opacity:1},200)}).css({opacity:0}).attr("src",c).removeAttr("data-lazy").removeClass("slick-loading")})}var c,d,e,f,b=this;b.options.centerMode===!0?b.options.infinite===!0?(e=b.currentSlide+(b.options.slidesToShow/2+1),f=e+b.options.slidesToShow+2):(e=Math.max(0,b.currentSlide-(b.options.slidesToShow/2+1)),f=2+(b.options.slidesToShow/2+1)+b.currentSlide):(e=b.options.infinite?b.options.slidesToShow+b.currentSlide:b.currentSlide,f=e+b.options.slidesToShow,b.options.fade===!0&&(e>0&&e--,f<=b.slideCount&&f++)),c=b.$slider.find(".slick-slide").slice(e,f),g(c),b.slideCount<=b.options.slidesToShow?(d=b.$slider.find(".slick-slide"),g(d)):b.currentSlide>=b.slideCount-b.options.slidesToShow?(d=b.$slider.find(".slick-cloned").slice(0,b.options.slidesToShow),g(d)):0===b.currentSlide&&(d=b.$slider.find(".slick-cloned").slice(-1*b.options.slidesToShow),g(d))},b.prototype.loadSlider=function(){var a=this;a.setPosition(),a.$slideTrack.css({opacity:1}),a.$slider.removeClass("slick-loading"),a.initUI(),"progressive"===a.options.lazyLoad&&a.progressiveLazyLoad()},b.prototype.postSlide=function(a){var b=this;null!==b.options.onAfterChange&&b.options.onAfterChange.call(this,b,a),b.animating=!1,b.setPosition(),b.swipeLeft=null,b.options.autoplay===!0&&b.paused===!1&&b.autoPlay()},b.prototype.progressiveLazyLoad=function(){var c,d,b=this;c=a("img[data-lazy]",b.$slider).length,c>0&&(d=a("img[data-lazy]",b.$slider).first(),d.attr("src",d.attr("data-lazy")).removeClass("slick-loading").load(function(){d.removeAttr("data-lazy"),b.progressiveLazyLoad()}).error(function(){d.removeAttr("data-lazy"),b.progressiveLazyLoad()}))},b.prototype.refresh=function(){var b=this,c=b.currentSlide;b.destroy(),a.extend(b,b.initials),b.init(),b.changeSlide({data:{message:"index",index:c}},!0)},b.prototype.reinit=function(){var b=this;b.$slides=b.$slideTrack.children(b.options.slide).addClass("slick-slide"),b.slideCount=b.$slides.length,b.currentSlide>=b.slideCount&&0!==b.currentSlide&&(b.currentSlide=b.currentSlide-b.options.slidesToScroll),b.slideCount<=b.options.slidesToShow&&(b.currentSlide=0),b.setProps(),b.setupInfinite(),b.buildArrows(),b.updateArrows(),b.initArrowEvents(),b.buildDots(),b.updateDots(),b.initDotEvents(),b.options.focusOnSelect===!0&&a(b.options.slide,b.$slideTrack).on("click.slick",b.selectHandler),b.setSlideClasses(0),b.setPosition(),null!==b.options.onReInit&&b.options.onReInit.call(this,b)},b.prototype.removeSlide=function(a,b,c){var d=this;return"boolean"==typeof a?(b=a,a=b===!0?0:d.slideCount-1):a=b===!0?--a:a,d.slideCount<1||0>a||a>d.slideCount-1?!1:(d.unload(),c===!0?d.$slideTrack.children().remove():d.$slideTrack.children(this.options.slide).eq(a).remove(),d.$slides=d.$slideTrack.children(this.options.slide),d.$slideTrack.children(this.options.slide).detach(),d.$slideTrack.append(d.$slides),d.$slidesCache=d.$slides,d.reinit(),void 0)},b.prototype.setCSS=function(a){var d,e,b=this,c={};b.options.rtl===!0&&(a=-a),d="left"==b.positionProp?a+"px":"0px",e="top"==b.positionProp?a+"px":"0px",c[b.positionProp]=a,b.transformsEnabled===!1?b.$slideTrack.css(c):(c={},b.cssTransitions===!1?(c[b.animType]="translate("+d+", "+e+")",b.$slideTrack.css(c)):(c[b.animType]="translate3d("+d+", "+e+", 0px)",b.$slideTrack.css(c)))},b.prototype.setDimensions=function(){var b=this;if(b.options.vertical===!1?b.options.centerMode===!0&&b.$list.css({padding:"0px "+b.options.centerPadding}):(b.$list.height(b.$slides.first().outerHeight(!0)*b.options.slidesToShow),b.options.centerMode===!0&&b.$list.css({padding:b.options.centerPadding+" 0px"})),b.listWidth=b.$list.width(),b.listHeight=b.$list.height(),b.options.vertical===!1&&b.options.variableWidth===!1)b.slideWidth=Math.ceil(b.listWidth/b.options.slidesToShow),b.$slideTrack.width(Math.ceil(b.slideWidth*b.$slideTrack.children(".slick-slide").length));else if(b.options.variableWidth===!0){var c=0;b.slideWidth=Math.ceil(b.listWidth/b.options.slidesToShow),b.$slideTrack.children(".slick-slide").each(function(){c+=Math.ceil(a(this).outerWidth(!0))}),b.$slideTrack.width(Math.ceil(c)+1)}else b.slideWidth=Math.ceil(b.listWidth),b.$slideTrack.height(Math.ceil(b.$slides.first().outerHeight(!0)*b.$slideTrack.children(".slick-slide").length));var d=b.$slides.first().outerWidth(!0)-b.$slides.first().width();b.options.variableWidth===!1&&b.$slideTrack.children(".slick-slide").width(b.slideWidth-d)},b.prototype.setFade=function(){var c,b=this;b.$slides.each(function(d,e){c=-1*b.slideWidth*d,b.options.rtl===!0?a(e).css({position:"relative",right:c,top:0,zIndex:800,opacity:0}):a(e).css({position:"relative",left:c,top:0,zIndex:800,opacity:0})}),b.$slides.eq(b.currentSlide).css({zIndex:900,opacity:1})},b.prototype.setHeight=function(){var a=this;if(1===a.options.slidesToShow&&a.options.adaptiveHeight===!0&&a.options.vertical===!1){var b=a.$slides.eq(a.currentSlide).outerHeight(!0);a.$list.css("height",b)}},b.prototype.setPosition=function(){var a=this;a.setDimensions(),a.setHeight(),a.options.fade===!1?a.setCSS(a.getLeft(a.currentSlide)):a.setFade(),null!==a.options.onSetPosition&&a.options.onSetPosition.call(this,a)},b.prototype.setProps=function(){var a=this,b=document.body.style;a.positionProp=a.options.vertical===!0?"top":"left","top"===a.positionProp?a.$slider.addClass("slick-vertical"):a.$slider.removeClass("slick-vertical"),(void 0!==b.WebkitTransition||void 0!==b.MozTransition||void 0!==b.msTransition)&&a.options.useCSS===!0&&(a.cssTransitions=!0),void 0!==b.OTransform&&(a.animType="OTransform",a.transformType="-o-transform",a.transitionType="OTransition",void 0===b.perspectiveProperty&&void 0===b.webkitPerspective&&(a.animType=!1)),void 0!==b.MozTransform&&(a.animType="MozTransform",a.transformType="-moz-transform",a.transitionType="MozTransition",void 0===b.perspectiveProperty&&void 0===b.MozPerspective&&(a.animType=!1)),void 0!==b.webkitTransform&&(a.animType="webkitTransform",a.transformType="-webkit-transform",a.transitionType="webkitTransition",void 0===b.perspectiveProperty&&void 0===b.webkitPerspective&&(a.animType=!1)),void 0!==b.msTransform&&(a.animType="msTransform",a.transformType="-ms-transform",a.transitionType="msTransition",void 0===b.msTransform&&(a.animType=!1)),void 0!==b.transform&&a.animType!==!1&&(a.animType="transform",a.transformType="transform",a.transitionType="transition"),a.transformsEnabled=null!==a.animType&&a.animType!==!1},b.prototype.setSlideClasses=function(a){var c,d,e,f,b=this;b.$slider.find(".slick-slide").removeClass("slick-active").removeClass("slick-center"),d=b.$slider.find(".slick-slide"),b.options.centerMode===!0?(c=Math.floor(b.options.slidesToShow/2),b.options.infinite===!0&&(a>=c&&a<=b.slideCount-1-c?b.$slides.slice(a-c,a+c+1).addClass("slick-active"):(e=b.options.slidesToShow+a,d.slice(e-c+1,e+c+2).addClass("slick-active")),0===a?d.eq(d.length-1-b.options.slidesToShow).addClass("slick-center"):a===b.slideCount-1&&d.eq(b.options.slidesToShow).addClass("slick-center")),b.$slides.eq(a).addClass("slick-center")):a>=0&&a<=b.slideCount-b.options.slidesToShow?b.$slides.slice(a,a+b.options.slidesToShow).addClass("slick-active"):d.length<=b.options.slidesToShow?d.addClass("slick-active"):(f=b.slideCount%b.options.slidesToShow,e=b.options.infinite===!0?b.options.slidesToShow+a:a,b.options.slidesToShow==b.options.slidesToScroll&&b.slideCount-a<b.options.slidesToShow?d.slice(e-(b.options.slidesToShow-f),e+f).addClass("slick-active"):d.slice(e,e+b.options.slidesToShow).addClass("slick-active")),"ondemand"===b.options.lazyLoad&&b.lazyLoad()},b.prototype.setupInfinite=function(){var c,d,e,b=this;if(b.options.fade===!0&&(b.options.centerMode=!1),b.options.infinite===!0&&b.options.fade===!1&&(d=null,b.slideCount>b.options.slidesToShow)){for(e=b.options.centerMode===!0?b.options.slidesToShow+1:b.options.slidesToShow,c=b.slideCount;c>b.slideCount-e;c-=1)d=c-1,a(b.$slides[d]).clone(!0).attr("id","").attr("index",d-b.slideCount).prependTo(b.$slideTrack).addClass("slick-cloned");for(c=0;e>c;c+=1)d=c,a(b.$slides[d]).clone(!0).attr("id","").attr("index",d+b.slideCount).appendTo(b.$slideTrack).addClass("slick-cloned");b.$slideTrack.find(".slick-cloned").find("[id]").each(function(){a(this).attr("id","")})}},b.prototype.selectHandler=function(b){var c=this,d=parseInt(a(b.target).parents(".slick-slide").attr("index"));return d||(d=0),c.slideCount<=c.options.slidesToShow?(c.$slider.find(".slick-slide").removeClass("slick-active"),c.$slides.eq(d).addClass("slick-active"),c.options.centerMode===!0&&(c.$slider.find(".slick-slide").removeClass("slick-center"),c.$slides.eq(d).addClass("slick-center")),c.asNavFor(d),void 0):(c.slideHandler(d),void 0)},b.prototype.slideHandler=function(a,b,c){var d,e,f,g,i=null,j=this;return b=b||!1,j.animating===!0&&j.options.waitForAnimate===!0||j.options.fade===!0&&j.currentSlide===a||j.slideCount<=j.options.slidesToShow?void 0:(b===!1&&j.asNavFor(a),d=a,i=j.getLeft(d),g=j.getLeft(j.currentSlide),j.currentLeft=null===j.swipeLeft?g:j.swipeLeft,j.options.infinite===!1&&j.options.centerMode===!1&&(0>a||a>j.getDotCount()*j.options.slidesToScroll)?(j.options.fade===!1&&(d=j.currentSlide,c!==!0?j.animateSlide(g,function(){j.postSlide(d)}):j.postSlide(d)),void 0):j.options.infinite===!1&&j.options.centerMode===!0&&(0>a||a>j.slideCount-j.options.slidesToScroll)?(j.options.fade===!1&&(d=j.currentSlide,c!==!0?j.animateSlide(g,function(){j.postSlide(d)}):j.postSlide(d)),void 0):(j.options.autoplay===!0&&clearInterval(j.autoPlayTimer),e=0>d?0!==j.slideCount%j.options.slidesToScroll?j.slideCount-j.slideCount%j.options.slidesToScroll:j.slideCount+d:d>=j.slideCount?0!==j.slideCount%j.options.slidesToScroll?0:d-j.slideCount:d,j.animating=!0,null!==j.options.onBeforeChange&&a!==j.currentSlide&&j.options.onBeforeChange.call(this,j,j.currentSlide,e),f=j.currentSlide,j.currentSlide=e,j.setSlideClasses(j.currentSlide),j.updateDots(),j.updateArrows(),j.options.fade===!0?(c!==!0?j.fadeSlide(f,e,function(){j.postSlide(e)}):j.postSlide(e),void 0):(c!==!0?j.animateSlide(i,function(){j.postSlide(e)}):j.postSlide(e),void 0)))},b.prototype.startLoad=function(){var a=this;a.options.arrows===!0&&a.slideCount>a.options.slidesToShow&&(a.$prevArrow.hide(),a.$nextArrow.hide()),a.options.dots===!0&&a.slideCount>a.options.slidesToShow&&a.$dots.hide(),a.$slider.addClass("slick-loading")},b.prototype.swipeDirection=function(){var a,b,c,d,e=this;return a=e.touchObject.startX-e.touchObject.curX,b=e.touchObject.startY-e.touchObject.curY,c=Math.atan2(b,a),d=Math.round(180*c/Math.PI),0>d&&(d=360-Math.abs(d)),45>=d&&d>=0?e.options.rtl===!1?"left":"right":360>=d&&d>=315?e.options.rtl===!1?"left":"right":d>=135&&225>=d?e.options.rtl===!1?"right":"left":"vertical"},b.prototype.swipeEnd=function(){var b=this;if(b.dragging=!1,b.shouldClick=b.touchObject.swipeLength>10?!1:!0,void 0===b.touchObject.curX)return!1;if(b.touchObject.swipeLength>=b.touchObject.minSwipe)switch(b.swipeDirection()){case"left":b.slideHandler(b.currentSlide+b.getSlideCount()),b.currentDirection=0,b.touchObject={};break;case"right":b.slideHandler(b.currentSlide-b.getSlideCount()),b.currentDirection=1,b.touchObject={}}else b.touchObject.startX!==b.touchObject.curX&&(b.slideHandler(b.currentSlide),b.touchObject={})},b.prototype.swipeHandler=function(a){var b=this;if(!(b.options.swipe===!1||"ontouchend"in document&&b.options.swipe===!1||b.options.draggable===!1&&-1!==a.type.indexOf("mouse")))switch(b.touchObject.fingerCount=a.originalEvent&&void 0!==a.originalEvent.touches?a.originalEvent.touches.length:1,b.touchObject.minSwipe=b.listWidth/b.options.touchThreshold,a.data.action){case"start":b.swipeStart(a);break;case"move":b.swipeMove(a);break;case"end":b.swipeEnd(a)}},b.prototype.swipeMove=function(a){var c,d,e,f,b=this;return f=void 0!==a.originalEvent?a.originalEvent.touches:null,!b.dragging||f&&1!==f.length?!1:(c=b.getLeft(b.currentSlide),b.touchObject.curX=void 0!==f?f[0].pageX:a.clientX,b.touchObject.curY=void 0!==f?f[0].pageY:a.clientY,b.touchObject.swipeLength=Math.round(Math.sqrt(Math.pow(b.touchObject.curX-b.touchObject.startX,2))),d=b.swipeDirection(),"vertical"!==d?(void 0!==a.originalEvent&&b.touchObject.swipeLength>4&&a.preventDefault(),e=(b.options.rtl===!1?1:-1)*(b.touchObject.curX>b.touchObject.startX?1:-1),b.swipeLeft=b.options.vertical===!1?c+b.touchObject.swipeLength*e:c+b.touchObject.swipeLength*(b.$list.height()/b.listWidth)*e,b.options.fade===!0||b.options.touchMove===!1?!1:b.animating===!0?(b.swipeLeft=null,!1):(b.setCSS(b.swipeLeft),void 0)):void 0)},b.prototype.swipeStart=function(a){var c,b=this;return 1!==b.touchObject.fingerCount||b.slideCount<=b.options.slidesToShow?(b.touchObject={},!1):(void 0!==a.originalEvent&&void 0!==a.originalEvent.touches&&(c=a.originalEvent.touches[0]),b.touchObject.startX=b.touchObject.curX=void 0!==c?c.pageX:a.clientX,b.touchObject.startY=b.touchObject.curY=void 0!==c?c.pageY:a.clientY,b.dragging=!0,void 0)},b.prototype.unfilterSlides=function(){var a=this;null!==a.$slidesCache&&(a.unload(),a.$slideTrack.children(this.options.slide).detach(),a.$slidesCache.appendTo(a.$slideTrack),a.reinit())},b.prototype.unload=function(){var b=this;a(".slick-cloned",b.$slider).remove(),b.$dots&&b.$dots.remove(),b.$prevArrow&&"object"!=typeof b.options.prevArrow&&b.$prevArrow.remove(),b.$nextArrow&&"object"!=typeof b.options.nextArrow&&b.$nextArrow.remove(),b.$slides.removeClass("slick-slide slick-active slick-visible").css("width","")},b.prototype.updateArrows=function(){var b,a=this;b=Math.floor(a.options.slidesToShow/2),a.options.arrows===!0&&a.options.infinite!==!0&&a.slideCount>a.options.slidesToShow&&(a.$prevArrow.removeClass("slick-disabled"),a.$nextArrow.removeClass("slick-disabled"),0===a.currentSlide?(a.$prevArrow.addClass("slick-disabled"),a.$nextArrow.removeClass("slick-disabled")):a.currentSlide>=a.slideCount-a.options.slidesToShow&&a.options.centerMode===!1?(a.$nextArrow.addClass("slick-disabled"),a.$prevArrow.removeClass("slick-disabled")):a.currentSlide>a.slideCount-a.options.slidesToShow+b&&a.options.centerMode===!0&&(a.$nextArrow.addClass("slick-disabled"),a.$prevArrow.removeClass("slick-disabled")))},b.prototype.updateDots=function(){var a=this;null!==a.$dots&&(a.$dots.find("li").removeClass("slick-active"),a.$dots.find("li").eq(Math.floor(a.currentSlide/a.options.slidesToScroll)).addClass("slick-active"))},a.fn.slick=function(a){var c=this;return c.each(function(c,d){d.slick=new b(d,a)})},a.fn.slickAdd=function(a,b,c){var d=this;return d.each(function(d,e){e.slick.addSlide(a,b,c)})},a.fn.slickCurrentSlide=function(){var a=this;return a.get(0).slick.getCurrent()},a.fn.slickFilter=function(a){var b=this;return b.each(function(b,c){c.slick.filterSlides(a)})},a.fn.slickGoTo=function(a,b){var c=this;return c.each(function(c,d){d.slick.changeSlide({data:{message:"index",index:parseInt(a)}},b)})},a.fn.slickNext=function(){var a=this;return a.each(function(a,b){b.slick.changeSlide({data:{message:"next"}})})},a.fn.slickPause=function(){var a=this;return a.each(function(a,b){b.slick.autoPlayClear(),b.slick.paused=!0})},a.fn.slickPlay=function(){var a=this;return a.each(function(a,b){b.slick.paused=!1,b.slick.autoPlay()})},a.fn.slickPrev=function(){var a=this;return a.each(function(a,b){b.slick.changeSlide({data:{message:"previous"}})})},a.fn.slickRemove=function(a,b){var c=this;return c.each(function(c,d){d.slick.removeSlide(a,b)})},a.fn.slickRemoveAll=function(){var a=this;return a.each(function(a,b){b.slick.removeSlide(null,null,!0)})},a.fn.slickGetOption=function(a){var b=this;return b.get(0).slick.options[a]},a.fn.slickSetOption=function(a,b,c){var d=this;return d.each(function(d,e){e.slick.options[a]=b,c===!0&&(e.slick.unload(),e.slick.reinit())})},a.fn.slickUnfilter=function(){var a=this;return a.each(function(a,b){b.slick.unfilterSlides()})},a.fn.unslick=function(){var a=this;return a.each(function(a,b){b.slick&&b.slick.destroy()})},a.fn.getSlick=function(){var a=null,b=this;return b.each(function(b,c){a=c.slick}),a}});;/*
 * share-selection: Medium like popover menu to share on Twitter or by email any text selected on the page 
 *
 * -- Requires jQuery --
 * -- AMD compatible  --
 *
 * Author: Xavier Damman (@xdamman)
 * GIT: https://github.com/xdamman/share-selection
 * MIT License
 */

(function($) {

  var SelectionSharer = function(options) {

    var self = this;

    options = options || {};
    if(typeof options == 'string')
        options = { elements: options };

    this.sel = null;
    this.textSelection='';
    this.htmlSelection='';


    this.getSelectionText = function(sel) {
        var html = "", text = "";
        var sel = sel || window.getSelection();
        if (sel.rangeCount) {
            var container = document.createElement("div");
            for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                container.appendChild(sel.getRangeAt(i).cloneContents());
            }
            text = container.textContent;
            html = container.innerHTML
        }
        self.textSelection = text;
        self.htmlSelection = html || text;
        return text;
    };

    this.selectionDirection = function(selection) {
      var sel = selection || window.getSelection();
      var range = document.createRange();
      if(!sel.anchorNode) return 0;
      range.setStart(sel.anchorNode, sel.anchorOffset);
      range.setEnd(sel.focusNode, sel.focusOffset);
      var direction = (range.collapsed) ? "backward" : "forward";
      range.detach();
      return direction;
    };

    this.showPopunder = function() {
      self.popunder = self.popunder || document.getElementById('selectionSharerPopunder'); 

      var sel = window.getSelection(); 
      var selection = self.getSelectionText(sel);

      if(sel.isCollapsed || selection.length < 10 || !selection.match(/ /))
        return self.hidePopunder();

      if(self.popunder.classList.contains("fixed"))
        return self.popunder.style.bottom = 0;

      var range = sel.getRangeAt(0);
      var node = range.endContainer.parentNode; // The <p> where the selection ends

      // If the popunder is currently displayed
      if(self.popunder.classList.contains('show')) { 
        // If the popunder is already at the right place, we do nothing
        if(Math.ceil(self.popunder.getBoundingClientRect().top) == Math.ceil(node.getBoundingClientRect().bottom))
          return;

        // Otherwise, we first hide it and the we try again
        return self.hidePopunder(self.showPopunder);
      }

      if(node.nextElementSibling) {
        // We need to push down all the following siblings 
        self.pushSiblings(node);
      }
      else {
        // We need to append a new element to push all the content below 
        if(!self.placeholder) {
          self.placeholder = document.createElement('div');
          self.placeholder.className = 'selectionSharerPlaceholder';
        }
       
        // If we add a div between two <p> that have a 1em margin, the space between them
        // will become 2x 1em. So we give the placeholder a negative margin to avoid that
        var margin = window.getComputedStyle(node).marginBottom;
        self.placeholder.style.height = margin;
        self.placeholder.style.marginBottom = (-2 * parseInt(margin,10))+'px';
        node.parentNode.insertBefore(self.placeholder);
      }

      // scroll offset
      var offsetTop = window.pageYOffset + node.getBoundingClientRect().bottom;
      self.popunder.style.top = Math.ceil(offsetTop)+'px';

      setTimeout(function() {
        if(self.placeholder) self.placeholder.classList.add('show');
        self.popunder.classList.add('show');
      },0);

    };

    this.pushSiblings = function(el) {
      while(el=el.nextElementSibling) { el.classList.add('selectionSharer'); el.classList.add('moveDown'); }
    };

    this.hidePopunder = function(cb) {
      cb = cb || function() {};

      if(self.popunder == "fixed") {
        self.popunder.style.bottom = '-50px';
        return cb();
      }

      self.popunder.classList.remove('show');
      if(self.placeholder) self.placeholder.classList.remove('show');
      // We need to push back up all the siblings
      var els = document.getElementsByClassName('moveDown');
      while(el=els[0]) {
          el.classList.remove('moveDown');
      }

      // CSS3 transition takes 0.6s
      setTimeout(function() {
        if(self.placeholder) document.body.insertBefore(self.placeholder);
        cb();
      }, 600);

    };

    this.show = function(e) {
      setTimeout(function() {
        var sel = window.getSelection(); 
        var selection = self.getSelectionText(sel);
        if(!sel.isCollapsed && selection && selection.length>10 && selection.match(/ /)) {
          var range = sel.getRangeAt(0);
          var topOffset = range.getBoundingClientRect().top - 5;
          var top = topOffset + window.scrollY - self.$popover.height();
          var left = 0;
          if(e) {
            left = e.pageX;
          }
          else {
            var obj = sel.anchorNode.parentNode;
            left += obj.offsetWidth / 2;
            do {
              left += obj.offsetLeft;
            }
            while(obj = obj.offsetParent);
          }
          switch(self.selectionDirection(sel)) {
            case 'forward':
              left -= self.$popover.width();
              break;
            case 'backward':
              left += self.$popover.width();
              break;
            default:
              return;
          }
          self.$popover.removeClass("anim").css("top", top+10).css("left", left).show();
          setTimeout(function() {
            self.$popover.addClass("anim").css("top", top);
          }, 0);
        }
      }, 10);
    };

    this.hide = function(e) {
      self.$popover.hide();
    };

    this.smart_truncate = function(str, n){
        if (!str || !str.length) return str;
        var toLong = str.length>n,
            s_ = toLong ? str.substr(0,n-1) : str;
        s_ = toLong ? s_.substr(0,s_.lastIndexOf(' ')) : s_;
        return  toLong ? s_ +'...' : s_;
    };

    this.getRelatedTwitterAccounts = function() {
      var usernames = [];

      var creator = $('meta[name="twitter:creator"]').attr("content") || $('meta[name="twitter:creator"]').attr("value");
      if(creator) usernames.push(creator);


      // We scrape the page to find a link to http(s)://twitter.com/username
      var anchors = document.getElementsByTagName('a');
      for(var i=0, len=anchors.length;i<len;i++) { 
        if(anchors[i].attributes.href && typeof anchors[i].attributes.href.value == 'string') {
          var matches = anchors[i].attributes.href.value.match(/^https?:\/\/twitter\.com\/([a-z0-9_]{1,20})/i) 
          if(matches && matches.length > 1 && ['widgets','intent'].indexOf(matches[1])==-1)
            usernames.push(matches[1]); 
        } 
      }

      if(usernames.length > 0)
        return usernames.join(',');
      else
        return '';
    };

    this.shareTwitter = function(e) {
      e.preventDefault(); 

      if(!self.viaTwitterAccount) {
        self.viaTwitterAccount = $('meta[name="twitter:site"]').attr("content") || $('meta[name="twitter:site"]').attr("value") || "";
        self.viaTwitterAccount = self.viaTwitterAccount.replace(/@/,'');
      }

      if(!self.relatedTwitterAccounts) {
        self.relatedTwitterAccounts = self.getRelatedTwitterAccounts();
      }

      var text = "“"+self.smart_truncate(self.textSelection.trim(), 114)+"”";
      var url = 'http://twitter.com/intent/tweet?text='+encodeURIComponent(text)+'&related='+self.relatedTwitterAccounts+'&url='+encodeURIComponent(window.location.href);

      // We only show the via @twitter:site if we have enough room
      if(self.viaTwitterAccount && text.length < (120-6-self.viaTwitterAccount.length))
        url += '&via='+self.viaTwitterAccount;

      var w = 640, h=440;
      var left = (screen.width/2)-(w/2);
      var top = (screen.height/2)-(h/2)-100;
      window.open(url, "share_twitter", 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+',       top='+top+', left='+left);
      self.hide();
      return false;
    };

    this.shareEmail = function(e) {
      var text = self.htmlSelection.replace(/<p[^>]*>/ig,'\n').replace(/<\/p>|  /ig,'').trim();
      var email = {};
      email.subject = encodeURIComponent("Quote from "+document.title);
      email.body = encodeURIComponent("“"+text+"”")+"%0D%0A%0D%0AFrom: "+document.title+"%0D%0A"+window.location.href;
      $(this).attr("href","mailto:?subject="+email.subject+"&body="+email.body);
      self.hide();
      return true;
    };

    this.render = function() {
      var popoverHTML =  '<div class="selectionSharer" id="selectionSharerPopover" style="position:absolute;">'
                       + '  <div id="selectionSharerPopover-inner">'
                       + '    <ul>'
                       + '      <li><a class="action tweet" href="" title="Share this selection on Twitter" target="_blank">Tweet</a></li>'
                       + '      <li><a class="action email" href="" title="Share this selection by email" target="_blank"><svg width="20" height="20"><path stroke="#FFF" stroke-width="6" d="m16,25h82v60H16zl37,37q4,3 8,0l37-37M16,85l30-30m22,0 30,30"/></svg></a></li>'
                       + '    </ul>'
                       + '  </div>'
                       + '  <div class="selectionSharerPopover-clip"><span class="selectionSharerPopover-arrow"></span></div>'
                       + '</div>';

      var popunderHTML = '<div id="selectionSharerPopunder" class="selectionSharer">'
                       + '  <div id="selectionSharerPopunder-inner">'
                       + '    <label>Share this selection</label>'
                       + '    <ul>'
                       + '      <li><a class="action tweet" href="" title="Share this selection on Twitter" target="_blank">Tweet</a></li>'
                       + '      <li><a class="action email" href="" title="Share this selection by email" target="_blank"><svg width="20" height="20"><path stroke="#FFF" stroke-width="6" d="m16,25h82v60H16zl37,37q4,3 8,0l37-37M16,85l30-30m22,0 30,30"/></svg></a></li>'
                       + '    </ul>'
                       + '  </div>'
                       + '</div>';

      self.$popover = $(popoverHTML);
      self.$popover.find('a.tweet').click(self.shareTwitter);
      self.$popover.find('a.email').click(self.shareEmail);

      $('body').append(self.$popover);

      self.$popunder = $(popunderHTML);
      self.$popunder.find('a.tweet').click(self.shareTwitter);
      self.$popunder.find('a.email').click(self.shareEmail);
      $('body').append(self.$popunder);
    };

    this.setElements = function(elements) {
      if(typeof elements == 'string') elements = $(elements);
      self.$elements = elements instanceof $ ? elements : $(elements);
      self.$elements.mouseup(self.show).mousedown(self.hide).addClass("selectionShareable");

      self.$elements.bind('touchstart', function(e) {
        self.isMobile = true;
      });

      document.onselectionchange = self.selectionChanged;
    };

    this.selectionChanged = function(e) {
      if(!self.isMobile) return;

      if(self.lastSelectionChanged) {
        clearTimeout(self.lastSelectionChanged);
      }
      self.lastSelectionChanged = setTimeout(function() {
        self.showPopunder(e);
      }, 300);
    };

    this.render();

    if(options.elements) {
      this.setElements(options.elements);
    }

  };

  // jQuery plugin 
  // Usage: $( "p" ).selectionSharer();
  $.fn.selectionSharer = function() {
    var sharer = new SelectionSharer();
    sharer.setElements(this);
    return this; 
  };

  // For AMD / requirejs
  // Usage: require(["selection-sharer!"]); 
  //     or require(["selection-sharer"], function(selectionSharer) { var sharer = new SelectionSharer('p'); });
  if(typeof define == 'function') {
    define(function() { 
      SelectionSharer.load = function (name, req, onLoad, config) {
        var sharer = new SelectionSharer();
        sharer.setElements('p');
        onLoad();
      };
      return SelectionSharer; 
    });

  }
  else {
    // Registering SelectionSharer as a global
    // Usage: var sharer = new SelectionSharer('p');
    window.SelectionSharer = SelectionSharer;
  }
  
})(jQuery);

 
;/* ========================================================================
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
				
				var $media = $(".hero-organism > img");
				
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
					
					
					
				  	
/*
				  	var smsRequest = $.post( actionURL, request,
				  		function(data) {
					  		 console.log( "Before: "+status );
					  		 console.log( data );
					  	})
						.done(function() {
							console.log( "Done: "+status );
							$(".switch-input").removeClass("message-"+status);
							status = "sent";
							$(".switch-input").addClass("message-"+status);
							
							console.log( "Success" );
						})
						.fail(function() {
							$(".switch-input").removeClass("message-"+status);
							status = "fail";
							$(".switch-input").addClass("message-"+status);
						})
						.always(function() {
							console.log( "Always: "+status );
							setTimeout(function(){
								$(".switch-input")
									.removeClass("valid-number message-"+status);
									status = "closed";
							}, 1000);
						});
*/
					
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
;document.documentElement.className += ' js_active ';
document.documentElement.className += 'ontouchstart' in document.documentElement ? ' vc_mobile ' : ' vc_desktop ';
(function () {
  var prefix = ['-webkit-', '-o-', '-moz-', '-ms-', ""];
  for (var i in prefix) {
    if (prefix[i] + 'transform' in document.documentElement.style) document.documentElement.className += " vc_transform ";
  }
})();
/*
 On document ready jQuery will fire set of functions.
 If you want to override function behavior then copy it to your theme js file
 with the same name.
 */

jQuery(window).load(function () {


});
var vc_js = function () {
  vc_twitterBehaviour();
  vc_toggleBehaviour();
  vc_tabsBehaviour();
  vc_accordionBehaviour();
  vc_teaserGrid();
  vc_carouselBehaviour();
  vc_slidersBehaviour();
  vc_prettyPhoto();
  vc_googleplus();
  vc_pinterest();
  vc_progress_bar();
  vc_plugin_flexslider();
  vc_google_fonts();
  window.setTimeout(vc_waypoints, 1500);
};
jQuery(document).ready(function ($) {
  window.vc_js();
}); // END jQuery(document).ready

if (typeof window['vc_plugin_flexslider'] !== 'function') {
  function vc_plugin_flexslider($parent) {
    var $slider = $parent ? $parent.find('.wpb_flexslider') : jQuery('.wpb_flexslider');
    $slider.each(function () {
      var this_element = jQuery(this);
      var sliderSpeed = 800,
        sliderTimeout = parseInt(this_element.attr('data-interval')) * 1000,
        sliderFx = this_element.attr('data-flex_fx'),
        slideshow = true;
      if (sliderTimeout == 0) slideshow = false;

      this_element.is(':visible') && this_element.flexslider({
        animation:sliderFx,
        slideshow:slideshow,
        slideshowSpeed:sliderTimeout,
        sliderSpeed:sliderSpeed,
        smoothHeight:true
      });
    });
  }
}

/* Twitter
 ---------------------------------------------------------- */
if (typeof window['vc_twitterBehaviour'] !== 'function') {
  function vc_twitterBehaviour() {
    jQuery('.wpb_twitter_widget .tweets').each(function (index) {
      var this_element = jQuery(this),
        tw_name = this_element.attr('data-tw_name');
      tw_count = this_element.attr('data-tw_count');

      this_element.tweet({
        username:tw_name,
        join_text:"auto",
        avatar_size:0,
        count:tw_count,
        template:"{avatar}{join}{text}{time}",
        auto_join_text_default:"",
        auto_join_text_ed:"",
        auto_join_text_ing:"",
        auto_join_text_reply:"",
        auto_join_text_url:"",
        loading_text:'<span class="loading_tweets">loading tweets...</span>'
      });
    });
  }
}

/* Google plus
 ---------------------------------------------------------- */
if (typeof window['vc_googleplus'] !== 'function') {
  function vc_googleplus() {
    if (jQuery('.wpb_googleplus').length > 0) {
      (function () {
        var po = document.createElement('script');
        po.type = 'text/javascript';
        po.async = true;
        po.src = 'https://apis.google.com/js/plusone.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(po, s);
      })();
    }
  }
}

/* Pinterest
 ---------------------------------------------------------- */
if (typeof window['vc_pinterest'] !== 'function') {
  function vc_pinterest() {
    if (jQuery('.wpb_pinterest').length > 0) {
      (function () {
        var po = document.createElement('script');
        po.type = 'text/javascript';
        po.async = true;
        po.src = 'http://assets.pinterest.com/js/pinit.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(po, s);
        //<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
      })();
    }
  }
}

/* Progress bar
 ---------------------------------------------------------- */
if (typeof window['vc_progress_bar'] !== 'function') {
  function vc_progress_bar() {
    if (typeof jQuery.fn.waypoint !== 'undefined') {

      jQuery('.vc_progress_bar').waypoint(function () {
        jQuery(this).find('.vc_single_bar').each(function (index) {
          var $this = jQuery(this),
            bar = $this.find('.vc_bar'),
            val = bar.data('percentage-value');

          setTimeout(function () {
            bar.css({"width":val + '%'});
          }, index * 200);
        });
      }, { offset:'85%' });
    }
  }
}

/* Waypoints magic
 ---------------------------------------------------------- */
if (typeof window['vc_waypoints'] !== 'function') {
  function vc_waypoints() {
    if (typeof jQuery.fn.waypoint !== 'undefined') {
      jQuery('.wpb_animate_when_almost_visible:not(.wpb_start_animation)').waypoint(function () {
        jQuery(this).addClass('wpb_start_animation');
      }, { offset:'85%' });
    }
  }
}

/* Toggle
 ---------------------------------------------------------- */
if (typeof window['vc_toggleBehaviour'] !== 'function') {
  function vc_toggleBehaviour() {
    jQuery(".wpb_toggle").unbind('click').click(function (e) {
      if (jQuery(this).next().is(':animated')) {
        return false;
      }
      if (jQuery(this).hasClass('wpb_toggle_title_active')) {
        jQuery(this).removeClass('wpb_toggle_title_active').next().slideUp(500);
      } else {
        jQuery(this).addClass('wpb_toggle_title_active').next().slideDown(500);
      }
    });
    jQuery('.wpb_toggle_content').each(function (index) {
      if (jQuery(this).next().is('h4.wpb_toggle') == false) {
        jQuery('<div class="last_toggle_el_margin"></div>').insertAfter(this);
      }
    });
  }
}

/* Tabs + Tours
 ---------------------------------------------------------- */
if (typeof window['vc_tabsBehaviour'] !== 'function') {
  function vc_tabsBehaviour($tab) {
    jQuery(function ($) {
      $(document.body).off('click.preview', 'a')
    });
    var $call = $tab || jQuery('.wpb_tabs, .wpb_tour'),
      ver = jQuery.ui && jQuery.ui.version ? jQuery.ui.version.split('.') : '1.10',
      old_version = parseInt(ver[0]) == 1 && parseInt(ver[1]) < 9;
    // if($call.hasClass('ui-widget')) $call.tabs('destroy');
    $call.each(function (index) {
      var $tabs,
        interval = jQuery(this).attr("data-interval"),
        tabs_array = [];
      //
      $tabs = jQuery(this).find('.wpb_tour_tabs_wrapper').tabs({
        show:function (event, ui) {
          wpb_prepare_tab_content(event, ui);
        },
        beforeActivate: function(event, ui) {
          ui.newPanel.index() !== 1 && ui.newPanel.find('.vc_pie_chart:not(.vc_ready)');
        },
        activate:function (event, ui) {
          wpb_prepare_tab_content(event, ui);
        }
      }).tabs('rotate', interval * 1000);

      jQuery(this).find('.wpb_tab').each(function () {
        tabs_array.push(this.id);
      });

      jQuery(this).find('.wpb_tabs_nav a').click(function (e) {
        e.preventDefault();
        if (jQuery.inArray(jQuery(this).attr('href'), tabs_array)) {
          if (old_version) {
            $tabs.tabs("select", jQuery(this).attr('href'));
          } else {
            $tabs.tabs("option", "active", jQuery(jQuery(this).attr('href')).index() - 1);
          }
          return false;
        }
      });

      jQuery(this).find('.wpb_prev_slide a, .wpb_next_slide a').click(function (e) {
        e.preventDefault();
        if (old_version) {
          var index = $tabs.tabs('option', 'selected');
          if (jQuery(this).parent().hasClass('wpb_next_slide')) {
            index++;
          }
          else {
            index--;
          }
          if (index < 0) {
            index = $tabs.tabs("length") - 1;
          }
          else if (index >= $tabs.tabs("length")) {
            index = 0;
          }
          $tabs.tabs("select", index);
        } else {
          var index = $tabs.tabs("option", "active"),
            length = $tabs.find('.wpb_tab').length;

          if (jQuery(this).parent().hasClass('wpb_next_slide')) {
            index = (index + 1) >= length ? 0 : index + 1;
          } else {
            index = index - 1 < 0 ? length - 1 : index - 1;
          }

          $tabs.tabs("option", "active", index);
        }

      });

    });
  }
}

/* Tabs + Tours
 ---------------------------------------------------------- */
if (typeof window['vc_accordionBehaviour'] !== 'function') {
  function vc_accordionBehaviour() {
    jQuery('.wpb_accordion').each(function (index) {
      var $tabs,
        interval = jQuery(this).attr("data-interval"),
        active_tab = !isNaN(jQuery(this).data('active-tab')) && parseInt(jQuery(this).data('active-tab')) > 0 ? parseInt(jQuery(this).data('active-tab')) - 1 : false,
        collapsible = active_tab === false || jQuery(this).data('collapsible') === 'yes';
      //
      $tabs = jQuery(this).find('.wpb_accordion_wrapper').accordion({
        header:"> div > h3",
        autoHeight:false,
        heightStyle:"content",
        active:active_tab,
        collapsible:collapsible,
        navigation:true,
        activate: vc_accordionActivate,
        change:function (event, ui) {
          if (jQuery.fn.isotope != undefined) {
            ui.newContent.find('.isotope').isotope("layout");
          }
          vc_carouselBehaviour(ui.newPanel);
        }
      });
      //.tabs().tabs('rotate', interval*1000, true);
    });
  }
}

/* Teaser grid: isotope
 ---------------------------------------------------------- */
if (typeof window['vc_teaserGrid'] !== 'function') {
  function vc_teaserGrid() {
    var layout_modes = {
      fitrows:'fitRows',
      masonry:'masonry'
    }
    jQuery('.wpb_grid .teaser_grid_container:not(.wpb_carousel), .wpb_filtered_grid .teaser_grid_container:not(.wpb_carousel)').each(function () {
      var $container = jQuery(this);
      var $thumbs = $container.find('.wpb_thumbnails');
      var layout_mode = $thumbs.attr('data-layout-mode');
      $thumbs.isotope({
        // options
        itemSelector:'.isotope-item',
        layoutMode:(layout_modes[layout_mode] == undefined ? 'fitRows' : layout_modes[layout_mode])
      });
      $container.find('.categories_filter a').data('isotope', $thumbs).click(function (e) {
        e.preventDefault();
        var $thumbs = jQuery(this).data('isotope');
        jQuery(this).parent().parent().find('.active').removeClass('active');
        jQuery(this).parent().addClass('active');
        $thumbs.isotope({filter:jQuery(this).attr('data-filter')});
      });
      jQuery(window).bind('load resize', function () {
        $thumbs.isotope("layout");
      });
    });

    /*
     var isotope = jQuery('.wpb_grid ul.thumbnails');
     if ( isotope.length > 0 ) {
     isotope.isotope({
     // options
     itemSelector : '.isotope-item',
     layoutMode : 'fitRows'
     });
     jQuery(window).load(function() {
     isotope.isotope("layout");
     });
     }
     */
  }
}

if (typeof window['vc_carouselBehaviour'] !== 'function') {
  function vc_carouselBehaviour($parent) {
    var $carousel = $parent ? $parent.find(".wpb_carousel") : jQuery(".wpb_carousel");
    $carousel.each(function () {
      var $this = jQuery(this);
      if ($this.data('carousel_enabled') !== true && $this.is(':visible')) {
        $this.data('carousel_enabled', true);
        var carousel_width = jQuery(this).width(),
          visible_count = getColumnsCount(jQuery(this)),
          carousel_speed = 500;
        if (jQuery(this).hasClass('columns_count_1')) {
          carousel_speed = 900;
        }
        /* Get margin-left value from the css grid and apply it to the carousele li items (margin-right), before carousele initialization */
        var carousele_li = jQuery(this).find('.wpb_thumbnails-fluid li');
        carousele_li.css({"margin-right":carousele_li.css("margin-left"), "margin-left":0 });

        jQuery(this).find('.wpb_wrapper:eq(0)').jCarouselLite({
          btnNext:jQuery(this).find('.next'),
          btnPrev:jQuery(this).find('.prev'),
          visible:visible_count,
          speed:carousel_speed
        })
          .width('100%');//carousel_width

        var fluid_ul = jQuery(this).find('ul.wpb_thumbnails-fluid');
        fluid_ul.width(fluid_ul.width() + 300);

        jQuery(window).resize(function () {
          var before_resize = screen_size;
          screen_size = getSizeName();
          if (before_resize != screen_size) {
            window.setTimeout('location.reload()', 20);
          }
        });
      }

    });
    /*
     if(jQuery.fn.bxSlider !== undefined ) {
     jQuery('.bxslider').each(function(){
     var $slider = jQuery(this);
     $slider.bxSlider($slider.data('settings'));
     });
     }
     */
    if (window.Swiper !== undefined) {

      jQuery('.swiper-container').each(function () {
        var $this = jQuery(this),
          my_swiper,
          max_slide_size = 0,
          options = jQuery(this).data('settings');

        if (options.mode === 'vertical') {
          $this.find('.swiper-slide').each(function () {
            var height = jQuery(this).outerHeight(true);
            if (height > max_slide_size) max_slide_size = height;
          });
          $this.height(max_slide_size);
          $this.css('overflow', 'hidden');
        }
        jQuery(window).resize(function () {
          $this.find('.swiper-slide').each(function () {
            var height = jQuery(this).outerHeight(true);
            if (height > max_slide_size) max_slide_size = height;
          });
          $this.height(max_slide_size);
        });
        my_swiper = jQuery(this).swiper(jQuery.extend(options, {
          onFirstInit:function (swiper) {
            if (swiper.slides.length < 2) {
              $this.find('.vc_arrow-left,.vc_arrow-right').hide();
            } else if (swiper.activeIndex === 0 && swiper.params.loop !== true) {
              $this.find('.vc_arrow-left').hide();
            } else {
              $this.find('.vc_arrow-left').show();
            }
          },
          onSlideChangeStart:function (swiper) {
            if (swiper.slides.length > 1 && swiper.params.loop !== true) {
              if (swiper.activeIndex === 0) {
                $this.find('.vc_arrow-left').hide();
              } else {
                $this.find('.vc_arrow-left').show();
              }
              if (swiper.slides.length - 1 === swiper.activeIndex) {
                $this.find('.vc_arrow-right').hide();
              } else {
                $this.find('.vc_arrow-right').show();
              }
            }
          }
        }));
        $this.find('.vc_arrow-left').click(function (e) {
          e.preventDefault();
          my_swiper.swipePrev();
        });
        $this.find('.vc_arrow-right').click(function (e) {
          e.preventDefault();
          my_swiper.swipeNext();
        });
        my_swiper.reInit();
      });

    }

  }
}

if (typeof window['vc_slidersBehaviour'] !== 'function') {
  function vc_slidersBehaviour() {
    //var sliders_count = 0;
    jQuery('.wpb_gallery_slides').each(function (index) {
      var this_element = jQuery(this);
      var ss_count = 0;

      /*if ( this_element.hasClass('wpb_slider_fading') ) {
       var sliderSpeed = 500, sliderTimeout = this_element.attr('data-interval')*1000, slider_fx = 'fade';
       var current_ss;

       function slideshowOnBefore(currSlideElement, nextSlideElement, options) {
       jQuery(nextSlideElement).css({"position" : "absolute" });
       jQuery(nextSlideElement).find("div.description").animate({"opacity": 0}, 0);
       }

       function slideshowOnAfter(currSlideElement, nextSlideElement, options) {
       jQuery(nextSlideElement).find("div.description").animate({"opacity": 1}, 2000);

       jQuery(nextSlideElement).css({"position" : "static" });
       var new_h = jQuery(nextSlideElement).find('img').height();
       if ( jQuery.isNumeric(new_h) ) {
       //this_element.animate({ "height" : new_h }, sliderSpeed );
       }
       }

       this_element.find('ul')
       .before('<div class="ss_nav ss_nav_'+ss_count+'"></div><div class="wpb_fading_nav"><a id="next_'+ss_count+'" href="#next"></a> <a id="prev_'+ss_count+'" href="#prev"></a></div>')
       .cycle({
       fx: slider_fx, // choose your transition type, ex: fade, scrollUp, shuffle, etc...
       pause: 1,
       speed: sliderSpeed,
       timeout: sliderTimeout,
       delay: -ss_count * 1000,
       before: slideshowOnBefore,
       after:slideshowOnAfter,
       pager:  '.ss_nav_'+ss_count
       });
       //.find('.description').width(jQuery(this).width() - 20);
       ss_count++;
       }
       else*/
      if (this_element.hasClass('wpb_slider_nivo')) {
        var sliderSpeed = 800,
          sliderTimeout = this_element.attr('data-interval') * 1000;

        if (sliderTimeout == 0) sliderTimeout = 9999999999;

        this_element.find('.nivoSlider').nivoSlider({
          effect:'boxRainGrow,boxRain,boxRainReverse,boxRainGrowReverse', // Specify sets like: 'fold,fade,sliceDown'
          slices:15, // For slice animations
          boxCols:8, // For box animations
          boxRows:4, // For box animations
          animSpeed:sliderSpeed, // Slide transition speed
          pauseTime:sliderTimeout, // How long each slide will show
          startSlide:0, // Set starting Slide (0 index)
          directionNav:true, // Next & Prev navigation
          directionNavHide:true, // Only show on hover
          controlNav:true, // 1,2,3... navigation
          keyboardNav:false, // Use left & right arrows
          pauseOnHover:true, // Stop animation while hovering
          manualAdvance:false, // Force manual transitions
          prevText:'Prev', // Prev directionNav text
          nextText:'Next' // Next directionNav text
        });
      }
      else if (this_element.hasClass('wpb_flexslider') && 1 == 2) { /* TODO: remove this */
        /*
         var sliderSpeed = 800,
         sliderTimeout = this_element.attr('data-interval')*1000,
         sliderFx = this_element.attr('data-flex_fx'),
         slideshow = true;
         if ( sliderTimeout == 0 ) slideshow = false;

         this_element.flexslider({
         animation: sliderFx,
         slideshow: slideshow,
         slideshowSpeed: sliderTimeout,
         sliderSpeed: sliderSpeed,
         smoothHeight: true

         });
         */

        /*
         var $first_object = this_element.find('li:first').show().find('*:not(a)');

         $first_object.bind('load', function() {
         if(!this_element.find('.flex-control-nav').is('ol')) {
         this_element.flexslider({
         animation: sliderFx,
         slideshow: slideshow,
         slideshowSpeed: sliderTimeout,
         sliderSpeed: sliderSpeed,
         smoothHeight: true
         });
         }
         });

         window.setTimeout(function(){
         if(!this_element.find('.flex-control-nav').is('ol')) {
         this_element.flexslider({
         animation: sliderFx,
         slideshow: slideshow,
         slideshowSpeed: sliderTimeout,
         sliderSpeed: sliderSpeed,
         smoothHeight: true
         });
         }
         }, 5000);
         */
      }
      else if (this_element.hasClass('wpb_image_grid')) {
        var isotope = this_element.find('.wpb_image_grid_ul');
        isotope.isotope({
          // options
          itemSelector:'.isotope-item',
          layoutMode:'fitRows'
        });
        jQuery(window).load(function () {
          isotope.isotope("layout");
        });
      }
    });
  }
}

if (typeof window['vc_prettyPhoto'] !== 'function') {
  function vc_prettyPhoto() {
    try {
      // just in case. maybe prettyphoto isnt loaded on this site
      jQuery('a.prettyphoto, .gallery-icon a[href*=".jpg"]').prettyPhoto({
        animationSpeed:'normal', /* fast/slow/normal */
        padding:15, /* padding for each side of the picture */
        opacity:0.7, /* Value betwee 0 and 1 */
        showTitle:true, /* true/false */
        allowresize:true, /* true/false */
        counter_separator_label:'/', /* The separator for the gallery counter 1 "of" 2 */
        //theme: 'light_square', /* light_rounded / dark_rounded / light_square / dark_square */
        hideflash:false, /* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
        deeplinking:false, /* Allow prettyPhoto to update the url to enable deeplinking. */
        modal:false, /* If set to true, only the close button will close the window */
        callback:function () {
          var url = location.href;
          var hashtag = (url.indexOf('#!prettyPhoto')) ? true : false;
          if (hashtag) location.hash = "!";
        } /* Called when prettyPhoto is closed */,
        social_tools:''
      });
    } catch (err) {
    }
  }
}

if ( typeof window['vc_google_fonts'] !== 'function' ) {
    function vc_google_fonts() {
        return;
    }
}
/* Helper
 ---------------------------------------------------------- */
function getColumnsCount(el) {
  var find = false,
    i = 1;

  while (find == false) {
    if (el.hasClass('columns_count_' + i)) {
      find = true;
      return i;
    }
    i++;
  }
}

var screen_size = getSizeName();
function getSizeName() {
  var screen_size = '',
    screen_w = jQuery(window).width();

  if (screen_w > 1170) {
    screen_size = "desktop_wide";
  }
  else if (screen_w > 960 && screen_w < 1169) {
    screen_size = "desktop";
  }
  else if (screen_w > 768 && screen_w < 959) {
    screen_size = "tablet";
  }
  else if (screen_w > 300 && screen_w < 767) {
    screen_size = "mobile";
  }
  else if (screen_w < 300) {
    screen_size = "mobile_portrait";
  }
  return screen_size;
}


function loadScript(url, $obj, callback) {

  var script = document.createElement("script")
  script.type = "text/javascript";

  if (script.readyState) {  //IE
    script.onreadystatechange = function () {
      if (script.readyState == "loaded" ||
        script.readyState == "complete") {
        script.onreadystatechange = null;
        callback();
      }
    };
  } else {  //Others
    /*
     script.onload = function(){

     callback();
     };
     */
  }

  script.src = url;
  $obj.get(0).appendChild(script);
}

/**
 * Prepare html to correctly display inside tab container
 *
 * @param event - ui tab event 'show'
 * @param ui - jquery ui tabs object
 */

function wpb_prepare_tab_content(event, ui) {
  var panel = ui.panel || ui.newPanel,
      $pie_charts = panel.find('.vc_pie_chart:not(.vc_ready)'),
      $carousel = panel.find('[data-ride="vc_carousel"]'),
      $ui_panel, $google_maps;
  vc_carouselBehaviour();
  vc_plugin_flexslider(panel);
  $pie_charts.length && jQuery.fn.vcChat && $pie_charts.vcChat();
  $carousel.length && jQuery.fn.carousel && $carousel.carousel('resizeAction');
  $ui_panel = panel.find('.isotope');
  $google_maps = panel.find('.wpb_gmaps_widget');
  if ($ui_panel.length > 0) {
    $ui_panel.isotope("layout");
  }
  if ($google_maps.length && !$google_maps.is('.map_ready')) {
    var $frame = $google_maps.find('iframe');
    $frame.attr('src', $frame.attr('src'));
    $google_maps.addClass('map_ready');
  }
  if(panel.parents('.isotope').length) {
    panel.parents('.isotope').each(function(){
      jQuery(this).isotope("layout");
    });
  }
}
var vc_accordionActivate = function(event, ui) {
  var $pie_charts = ui.newPanel.find('.vc_pie_chart:not(.vc_ready)'),
    $carousel = ui.newPanel.find('[data-ride="vc_carousel"]');
  if (jQuery.fn.isotope != undefined) {
    ui.newPanel.find('.isotope').isotope("layout");
  }
  vc_carouselBehaviour(ui.newPanel);
  vc_plugin_flexslider(ui.newPanel);
  $pie_charts.length && jQuery.fn.vcChat && $pie_charts.vcChat();
  $carousel.length && jQuery.fn.carousel && $carousel.carousel('resizeAction');
  if(ui.newPanel.parents('.isotope').length) {
    ui.newPanel.parents('.isotope').each(function(){
      jQuery(this).isotope("layout");
    });
  }
}

