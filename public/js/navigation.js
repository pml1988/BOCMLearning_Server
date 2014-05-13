$(document).ready(function() {
    if($('.navigation-block').height() < $('.content-block').height())
    {
        $('.navigation-block').affix({
            offset: {
                top: 95,
                bottom: 60
            }
        });
    }

	
	// Menu Dropdown
	$('.main-navigation li ul').hide(); //Hide all sub menus
	$('.main-navigation li.current a').parent().find('ul').slideToggle('slow'); // Slide down the current sub menu
	$('.main-navigation li a').click(
		function () {
			$(this).parent().siblings().find('ul').slideUp('normal'); // Slide up all menus except the one clicked
			$(this).parent().find('ul').slideToggle('normal'); // Slide down the clicked sub menu
			return false;
		}
	);
	$('.main-navigation li a.no-submenu, .main-navigation li li a').click(
		function () {
			window.location.href=(this.href); // Open link instead of a sub menu
			return false;
		}
	);
	
});
