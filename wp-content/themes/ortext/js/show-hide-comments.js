// Show/Hide Comments
// Adapted from Corey Mahler code
// http://coreyjmahler.com/topics/create-a-button-to-showhide-wordpress-comments-with-a-click/

jQuery(document).ready(function() {
	// Get #comment-section div
	var commentsDiv = jQuery('#comments');
	// Only do this work if that div isn't empty
	if (commentsDiv.length) {
		// Hide #comments div by default
		jQuery(commentsDiv).hide();
		// When show/hide is clicked
		jQuery('#toggle-comments-vis').on('click', function(e) {
			e.preventDefault();
			// Show/hide the div using jQuery's toggle()
			jQuery(commentsDiv).toggle('fast', function() {
				// change the text of the anchor
				var anchor = jQuery('#toggle-comments-vis');
				var anchorText = anchor.html() == 'Comments' ? 'Hide Comments' : 'Comments';
				jQuery(anchor).html(anchorText);
			});
			// Scroll to bottom of page
			// http://stackoverflow.com/questions/1890995/jquery-scroll-to-bottom-of-page-iframe
//			jQuery('html, body').animate({ scrollTop: jQuery(document).height()-jQuery(window).height()},	1400, "swing" );
//			jQuery('body').scrollTop(20000);			
		});
	} // End of commentsDiv.length
	return false;
}); // End of Show/Hide Comments

