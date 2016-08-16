(function($) {
	/*
	 * CTX Sticky Navigation v.1
	 * Plugin to allow items with class name "ctx-sticky" to anchor to top of page on scroll and hide on mobile when scrolling down
	 * 
	 * TODO:
	 * 
	 */
    $.fn.ctxSticky = function(options) {
    	    	
        options = $.extend({
        	stickyItem : $(".ctx-sticky").parent(),
        	placeholder : "",
        	bp : "",
        	disableSmall : false
        }, options);
                
        $.fn.ctxSticky.settings = options;
        
      
        var setData = function(){
        	var s = options.stickyItem;
        	options.bp = ($(window).width() <= 500) ? "small" : "";
        	var topOffset = (options.bp === "small") ?  options.placeholder.offset().top+$(".ctx-sticky", s).height() : options.placeholder.offset().top ;
        	s.data("top", topOffset).data("bottom", topOffset+s.height()).data("height", s.height());
        	options.placeholder.height(s.height());
        }
        
        var getScrollTop = function(){
    		return $(window).scrollTop();
    	}
    	
    	var bindEvents = function(){
    		var s = options.stickyItem;    			
			var lastScrollTop = 0;
			
			$(window).scroll(
			    	
		    	$.throttle( 50, function(){
		    		
		    		var st = $(this).scrollTop();
	    		    if (st > lastScrollTop){
	    		    	options.scrollDirection = "down";
	    		    } else {
	    		    	options.scrollDirection = "up";
	    		    }
	    		    lastScrollTop = st;
		    		
		    		//console.log("TOP: "+options.stickyItem.data("top"));
		    		//console.log("BOTTOM: "+options.stickyItem.data("bottom"));
		    		//console.log(getScrollTop());
	    		    //console.log(options.bp);
		    		
		    		checkSticky();
		    		
		    	})
		    	
		    );

			$(window).on("debouncedresize", function(){
		    	setData();

                var st = $(this).scrollTop();
                    if (st > lastScrollTop){
                        options.scrollDirection = "down";
                    } else {
                        options.scrollDirection = "up";
                    }
                    lastScrollTop = st;
                    
                    //console.log("TOP: "+options.stickyItem.data("top"));
                    //console.log("BOTTOM: "+options.stickyItem.data("bottom"));
                    //console.log(getScrollTop());
                    //console.log(options.bp);
                    
                    checkSticky();
		    });
    		
    		
    	}
    
    	
    	var checkSticky = function(){
    		var s = options.stickyItem;
    		
    		if(options.bp === "small"){
    			
    			if(options.scrollDirection == "up"){

    				if(getScrollTop() > s.data("bottom")){
    					
    					if(!options.disableSmall){
    						initSticky();
    					}
    					
    				}else{
    					reset();
    				}
    				
    			}else{
    				if(getScrollTop() > s.data("top")){
        				s.removeClass("init-sticky").removeAttr("style");
        			}
    			}
    			
    		}else{
    			
    			if(getScrollTop() > s.data("top")){
        			
    				initSticky();
        			
        		}else{
        			reset();
        		}
    			
    		}
    		
    		function initSticky(){
    			if(!s.hasClass("init-sticky")){
    				s.css("margin-top", "-"+s.data("height")+"px");
    				var animeHeight = (options.bp === "small") ? "-"+(options.placeholder.height()- $(".ctx-sticky", s).height())+"px" : "0";
    				var duration = (options.bp === "small") ? 300 : 300;
    				s.animate({
        			    marginTop: animeHeight
        			}, {
        			    duration: duration,
        			    easing: "swing"
        			});
    			}
    			s.addClass("init-sticky");
    		}
    		
    		function reset(){
    			if(getScrollTop() < s.data("top")){
    				s.removeClass("init-sticky").removeAttr("style");
    			}
    		}

    	}
    	
    	
    	var init = function(){
    		if(options.stickyItem.length > 0){
    			options.stickyItem.wrap("<div class='ctx-sticky-placeholder' />");
    			options.placeholder = $(".ctx-sticky-placeholder");
    			options.disableSmall = ($(".ctx-sticky").hasClass("disable-sticky-small")) ? true : false;
	    		setTimeout(function(){ setData(); }, 200);
	    		bindEvents();
    		}
    	}
    	
    	init();

        return $.fn.ctxSticky.settings;
    };
    
    $(document).ready(function(){
    	$(document).ctxSticky();
    });
    
   
})(jQuery);
