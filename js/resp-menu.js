/**
 * Created by michaelkoepke on 3/19/14.
 */

jQuery(document).ready(function($) {

  $("#header_nav").before('<div id="header-menu-icon"></div>');
	$("#header-menu-icon").click(function() {
		$("#header_nav").slideToggle();
	});
	$(window).resize(function(){
		if(window.innerWidth > 649) {
			$("#header_nav").removeAttr("style");
		}
	});

	// add hover class when list item is hovered
	$("#header_nav li").hover(function() {
        $(this).addClass('hover');
	}, function() {
        $(this).removeClass('hover');
    });

	$( function()
	{
		$( '#header_nav li:has(ul)' ).doubleTapToGo();
	});
});



