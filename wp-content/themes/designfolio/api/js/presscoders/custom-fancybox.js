/* Custom code for the fancybox lightbox */
jQuery(document).ready(function($) {

	/* Apply fancybox to multiple items */
	$("a[href$='.jpg'],a[href$='.jpeg'],a[href$='.png'],a[href$='.gif']").attr('class', 'group').attr('rel', 'group1');
	
	/* Apply fancybox to multiple items */
	$("dt.gallery-icon a").attr('class', 'group').attr('rel', 'group1');

	$("a.group").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	300, 
		'speedOut'		:	200,
		'autoScale'		:	true,
		'overlayShow'	:	true
	});
});