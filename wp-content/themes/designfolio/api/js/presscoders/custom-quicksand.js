/* Custom code for Quicksand. */
jQuery(document).ready(function($) {

function portfolio_quicksand() {

	// Setting up our variables
	var $filter;
	var $container;
	var $containerClone;
	var $filterLink;
	var $filteredItems

	// Set our filter
	$filter = $('#filters li.active a').attr('class');

	// Set our filter link
	$filterLink = $('#filters li a');

	// Set our container
	$container = $('ul.filterable-grid');

	// Clone our container
	$containerClone = $container.clone();

	// Apply our Quicksand to work on a click function
	// for each of the filter li link elements
	$filterLink.click(function(e)
	{
		// Remove the active class
		$('.filter li').removeClass('active');

		// Split each of the filter elements and override our filter
		$filter = $(this).attr('class').split(' ');

		// Apply the 'active' class to the clicked link
		$(this).parent().addClass('active');

		// If 'all' is selected, display all elements
		// else output all items referenced by the data-type
		if ($filter == 'all') {
			$filteredItems = $containerClone.find('li');
		}
		else {
			if( $('html').hasClass('ie7') ) {
				var all_items = null; // Initialize flag

				$filter = jQuery.grep($filter, function(value) {
					if( value == 'all' ) all_items = 1; // Set flag
					if( value.indexOf('ie7') < 0 ) {
						return true; // Ignore items that contain ie7
					}
				});

				if( all_items == 1 ) {
					$filteredItems = $containerClone.find('li');
				}
				else {
					$filteredItems = $containerClone.find('li[data-type~=' + $filter + ']');
				}
			}
			else {
				$filteredItems = $containerClone.find('li[data-type~=' + $filter + ']');
			}
		}

		// Finally call the Quicksand function
		$container.quicksand($filteredItems,
		{
			duration: 700,
			adjustHeight: 'auto'
		}, function() { // Callback function

			if(jQuery().fancybox) {
				/* This is a duplicate of the custom fancybox JavaScript, which needs reapplying after sorting. If that code changes this will need changing too. */
				$("a.group").fancybox({
					'transitionIn'	:	'elastic',
					'transitionOut'	:	'elastic',
					'speedIn'		:	300, 
					'speedOut'		:	200,
					'autoScale'		:	true,
					'overlayShow'	:	true
				});
			}
		});
	});
}

if(jQuery().quicksand) {

	portfolio_quicksand();

}

});