// Add form elements to correct place on widgets.php page
jQuery(document).ready(function($) {
	// Create div container
	$('.widget-liquid-right').append('<div id="widget_holder_dest"></div>');

	// Move div container with appendTo and make visible
	$('#custom_widget_form_container').appendTo('#widget_holder_dest').css({'display': 'block'});
});

// Add/delete custom widget areas
jQuery(document).ready(function($) {
	// Hide by default
	$("#custom-widgets-width-toggle").css({'display': 'none'});

	// Toggle custom widget width drop down visibility
	$("#custom-widgets-width-trigger").click(function () {
		$("#custom-widgets-width-toggle").toggle('fast');
		if( $("#custom-widgets-width-trigger").text() == "[+] Options" ) {
			$("#custom-widgets-width-trigger").html('<a>[-] <span style="font-style:italic;">Options</span></a>');
		}
		else {
			$("#custom-widgets-width-trigger").html('<a>[+] <span style="font-style:italic;">Options</span></a>');
		}
	});

	// Submit form and add a new custom widget area
	$( "#add_widget_area" ).click(function () {
		add_new_wa();
	});

	$('#widget_area_txt_input').bind('keydown',function(e){
		if( e.which == '13' ) { // Was 'Enter' pressed?
			add_new_wa();
		}
	});

	// This code is being referenced twice, so wrap it in a function to aviod duplication
	function add_new_wa() {
		var widget_area_type = $("#drp_widget_area_type").attr("value");
		var widget_label = $("#widget_area_txt_input").attr("value");
		var last_name;

		widget_label = widget_label.replace(/(<([^>]+)>)/ig,""); /* Get rid of HTML tags. */

		// Get unique custom widget instance number
		if( (last_name = $(".scroll_checkboxes input[type='hidden']:last").attr("name")) === undefined) {
			last_name = 0;
		}
		else {
			last_name = parseInt(last_name.match(/\d+/g)); // get the number from element name
		}

		// If empty label
		if( widget_label == "" ) {
			widget_label = "Custom Widget Area " + (last_name + 1);
		}

		// Add new custom widget area!
		$(".scroll_checkboxes").append('<input class="widget_area_hidden" name="' + custom_widget_options.widget_options_db_name + '[txt_custom_widget_area_' + (last_name + 1) + '_' + widget_area_type + ']" type="hidden" value="' + widget_label + '" />');
		
		// Reset widget area label input text box
		$("#widget_area_txt_input").val("");

		// Submit the form
		$( "form#add_new_widget_area" ).submit();
	}

	// Submit form and delete a custom widget area
	$( "#delete_widget_area" ).click(function () {
		var del_txt = $("#drp_delete_widget_area option:selected").text();
		var res1=confirm("Delete '" + del_txt + "'? This widget area may contain active widgets.");

		if (res1==true) {
			// Get current selected widget area to delete
			var widget_area_delete = $("#drp_delete_widget_area").attr("value");
			$(".scroll_checkboxes input[name='" + widget_area_delete + "'][type='hidden']").remove(); //remove the last input

			// Submit the form
			$( "form#add_new_widget_area" ).submit();

		}
	});
});