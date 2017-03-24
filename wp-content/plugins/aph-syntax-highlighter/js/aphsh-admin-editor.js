(function($) {
	$(document).ready(function(){
		var textarea = document.getElementById('aphsh-editor-code');
		tabOverride.set(textarea);
		$('#aphsh-editor-title button, #aphsh-cancel').click(function()
		{
			if (!$('#aphsh-other-options-container').is(':hidden'))
			{
				$('#aphsh-other-options').trigger('click');
			}
			$('#aphsh-editor-overlay, #aphsh-editor-wrap').hide();
		})
		$('#aphsh-other-options').click(function(){
			// $('#aphsh-other-options-container').slideToggle('fast');
			var $i_elm = $(this).children('i'),
				class_name = $i_elm.attr('class');
			if (class_name == 'aphsh-icon-circle-down')
			{
				$('#aphsh-other-options-container').slideDown('fast');
				$i_elm.attr('class', 'aphsh-icon-circle-up');
			} else {
				$('#aphsh-other-options-container').slideUp('fast');
				$i_elm.attr('class', 'aphsh-icon-circle-down');
			}
		})
		QTags.addButton( 'aphsh_quicktag', 'APH-SH', function(btn, textarea, ed)
		{
			var $textarea = $(ed.canvas),
				text = $textarea.val(),
				selStart = ed.canvas.selectionStart,
				selEnd = ed.canvas.selectionEnd,
				selection = '';
			
			if (selEnd - selStart)
				selection = text.substr(selStart, selEnd - selStart);
			
			$('#aphsh-editor-overlay').show();
			$('#aphsh-editor-wrap').show();
			$('#aphsh-editor-code').val(selection);
			
			// restore defaults
			$('#aphsh-overr-showln').removeAttr('checked');
			$('#aphsh-opt-showln').val('false');
			$('#aphsh-start-number').val('');
			
			$('#aphsh-html-script').val('false');
			$('#aphsh-title').val('');
			$('#aphsh-input-class-name').val('');
			$('#aphsh-highlight-lines').val('');
			
			$('#aphsh-overr-auto-links').removeAttr('checked');
			$('#aphsh-opt-auto-links').val('false');
			
			// Click submit button
			$('#aphsh-submit').click(function()
			{
					// Lang
				var new_language = 'lang:' + $('#aphsh-language').val(),
				
					// gutter:true
					override_line_number = $('#aphsh-overr-showln').is(':checked'),
					class_line_number = '';
					
				if (override_line_number) {
					
					var start_number = $.trim($('#aphsh-start-number').val()) ||  1,
						show_line_number = $.trim($('#aphsh-opt-showln').val()),
						data_start_number = show_line_number == 'true' ? ' start:' + start_number : '',
						class_line_number = ' gutter:' + show_line_number + data_start_number;
				}	
				
					// auto-links
				var override_auto_links = $('#aphsh-overr-auto-links').is(':checked'),
					class_auto_links = override_auto_links ? ' auto-links:' + $('#aphsh-opt-auto-links').val() : '',

					// class-name
					input_class_name = $.trim($('#aphsh-input-class-name').val()),
					add_class_name = input_class_name ? ' class:' + input_class_name : '';					
				
					// html-script:true
					html_script = $('#aphsh-html-script').val() == 'true' ? ' html-script:true' : '',
					
					// highlight:true
					highlight_line = $.trim($('#aphsh-highlight-lines').val()),
					data_highlight_line = highlight_line ? ' mark:' + highlight_line : '',
					
					// Encode TAG
					$div = $('<div/>'),
					clean_code = $.trim($('#aphsh-editor-code').val()),
					encoded_html = $div.text(clean_code).html(),
					$div.remove(),
					
					// Title
					title = $('#aphsh-title').val(),
					
					attr_title = title ? ' title="' + title + '"' : '';
					
					// Class name
					class_name = new_language + data_highlight_line + html_script + class_line_number + class_auto_links + add_class_name;
					
				var textBefore = text.substr(0, selStart),
					textAfter = text.substr(selStart + selection.length, text.length - selStart),
					content = '<pre class="' + class_name + '"' + attr_title + '>' + encoded_html + '</pre>';
				
				
				$textarea.val(textBefore + content + textAfter);
			
				// QTags.insertContent();
				$('#aphsh-editor-overlay').hide();
				$('#aphsh-editor-wrap').hide();
				if (!$('#aphsh-other-options-container').is(':hidden'))
				{
					$('#aphsh-other-options').trigger('click');
				}
				
			});
		});
	});
})(jQuery);
