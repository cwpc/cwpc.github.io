/* 
 * Toggles search on and off
 */
jQuery(document).ready(function($){
    $(".search-toggle").click(function(){
        $(".search-box-wrapper").slideToggle('instant', function(){
            $('.search-toggle').toggleClass('active');
        });
        return false;
    });
});

jQuery(document).ready(function($){
    $(".search-toggle-bottom").click(function(){
        $(".search-box-wrapper-bottom").slideToggle('instant', function(){
            $('.search-toggle-bottom').toggleClass('active');
			$("html,body").scrollTop($("html,body")[0].scrollHeight); 
        });	
        return false;
    });
});


/* on scrolling
http://stackoverflow.com/questions/21784903/complete-animation-then-scroll-to-div?rq=1
http://stackoverflow.com/questions/270612/scroll-to-bottom-of-div (but doesn't work)
http://stackoverflow.com/questions/7886630/how-to-scroll-to-bottom-of-div-when-using-slidetoggle  (must include in callback)
*/