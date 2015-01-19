// thanks to Virendra's TechTalk
// http://techtalk.virendrachandak.com/make-a-div-stick-to-top-when-scrolled-to/

// Wordpress runs jquery in noconflict mode (fix below)
// http://stackoverflow.com/questions/7975093/typeerror-undefined-is-not-a-function-evaluating-document
// wrapper function $ does a re-dfine to jQuery
(function ($) {

// This function will be executed when the user scrolls the page.
// $(window).scroll(function(e) {
function onScroll(e) {
    // Get the position of the location where the scroller starts.
    var scroller_anchor = $('#scroller-anchor').offset().top;
    
    // Check if the user has scrolled and the current position is after the scroller's start location and if its not already fixed at the top 
    if ($(this).scrollTop() >= scroller_anchor && $('#site-navigation').css('position') != 'fixed') 
    {    // Change the CSS of the scroller to highlight it and fix it at the top of the screen.
        $('#site-navigation').css({
            'position': 'fixed',
            'top': '0px'
        });
        // Changing the height of the scroller anchor to that of scroller so that there is no change in the overall height of the page.
        $('#scroller-anchor').css('height', '54px');
    } 
    else if ($(this).scrollTop() < scroller_anchor && $('#site-navigation').css('position') != 'relative') 
    {    // If the user has scrolled back to the location above the scroller anchor place it back into the content.
        
        // Change the height of the scroller anchor to 0 and now we will be adding the scroller back to the content.
        $('#scroller-anchor').css('height', '0px');
        
        // Change the CSS and put it back to its original position.
        $('#site-navigation').css({
            'position': 'relative'
        });
    }
};
document.addEventListener('scroll', onScroll);

}(jQuery));
