(function($) {
	var node_pre_onclick = '',
		show_line_number = true;
		show_language = true,
		data_line = '',
		$iframe_body = '',
		$aphsh_btn = '',
		default_lang = 'php',
		new_lang = default_lang,
		
		pre_tag_highlighted = 0,
		aphsh_tag_highlighted = 0,
		
		options = $.parseJSON($.trim($('#aphsh-json-user-options').text()));
	
    tinymce.create('tinymce.plugins.Aph_Syntax_Highlighter', {
        init : function(ed, url) 
		{
			ed.onKeyUp.add(function(){
				checkCursor();
			});
			
            ed.addButton('aphsh', {
				// Button title
                title : 'APH Syntax Highlighter',
				// Button class
				classes: 'aphsh-btn',
				// Button command
                cmd : 'aphsh',
				// Button image
                image : url + '/img/aphsh-button.png',
				// onClick event
				onClick: function(e)
				{
					$aphsh_btn = $(e.target).parent().parent();
				}
            });
			
			// When the button in the toolbar is clicked
			ed.addCommand('aphsh', function() 
			{

				/* Check cursor within pre or code, if it not empty 
				   fill the code editor with the text inside the <pre> tag
				   alsu add aother parameters
				*/
				
				var cursor_node = ed.selection.getNode(),
					tag_name = cursor_node.nodeName.toLowerCase(),
					code_value = ed.selection.getContent({format : 'text'}),
					highlight_line = '',
					override_line_number = 0,
					start_number = '',
					language = options['default-lang'],
					html_script = 'false',
					override_auto_links = 0,
					class_name = '',
					auto_links = 'false',
					show_ln = 'false',
					title = '';
				
				if (tag_name == 'pre')
				{
					$pre = $(cursor_node);
					var classes = $pre.attr('class');
					if (classes.match(/lang\s*:/))
					{
						/* fix space around colon => "  : " become ":" */
						classes = classes.replace(/\s*:\s*/, ':');
						list_classes = classes.split(' ');
						for (k in list_classes)
						{
							
							var split = list_classes[k].split(':'),
								value = $.trim(split[1]);
								
							if (list_classes[k].indexOf('lang') != -1)
							{
								language = value;
							}
							else if (list_classes[k].indexOf('mark') != -1)
							{
								highlight_line = value;
							} 
							else if (list_classes[k].indexOf('gutter') != -1)
							{
								override_line_number = 1;
								show_ln = value
							}
							else if (list_classes[k].indexOf('start') != -1)
							{
								start_number = value;
							}
							else if (list_classes[k].indexOf('html-script') != -1)
							{
								html_script = value;
							}
							else if (list_classes[k].indexOf('auto-links') != -1)
							{
								override_auto_links = 1;
								auto_links = value;
							}
							else if (list_classes[k].indexOf('class') != -1)
							{
								class_name = value.replace(/\s+/gi, ';');
							}
						}
						
						// set default value to aphsh textarea popup editor
						code_value       = $pre.text();
						
						/*
							when user click insert code, then we know that we want to change 
							the code within existing <pre> tag
						*/
						node_pre_onclick = $pre;
						
						title = $pre.attr('title');
					}
				}
				
				// lang:php
				$('#aphsh-language').val(language);
				// highlight:4
				$('#aphsh-highlight-lines').val(highlight_line);
				// HTML Script
				$('#aphsh-html-script').val(html_script);
				// insert code
				$('#aphsh-editor-code').val(code_value);
				
				// gutter:true
				if (override_line_number)
					$('#aphsh-overr-showln').attr('checked', 'checked');
				else
					$('#aphsh-overr-showln').removeAttr('checked');
				
				$('#aphsh-opt-showln').val(show_ln);
				// start:1
				$('#aphsh-start-number').val(start_number);
				
				// auto-links
				if (override_auto_links) {
					$('#aphsh-overr-auto-links').attr('checked', 'checked');
					$('#aphsh-opt-auto-links').val(auto_links);
				} else {
					$('#aphsh-overr-auto-links').removeAttr('checked');
					$('#aphsh-opt-auto-links').val('false');
				}
				
				// class-name
				$('#aphsh-input-class-name').val(class_name);
				
				$('#aphsh-editor-overlay').show();
				$('#aphsh-editor-wrap').show();
				
				// Other options
				if (override_line_number || override_auto_links || class_name || html_script == 'true' || title)
				{
					if ($('#aphsh-other-options-container').is(':hidden'))
					{
						$('#aphsh-other-options').trigger('click');
						$('#aphsh-editor-body').scrollTop(0);
					}
				} else {
					if (!$('#aphsh-other-options-container').is(':hidden'))
					{
						$('#aphsh-other-options').trigger('click');
					}
				}
				// Title
				$('#aphsh-title').val(title);
				
				
				/* If submit button is clicked then insert code to tinyMCE editor
				   using <pre> tag with class attribute
				*/
				$('#aphsh-submit').unbind('click').click(function(e)
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
						input_class_name = $.trim($('#aphsh-input-class-name').val().replace(/\s+/i, ';')),
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
				
					/* 
						If the cursor at the <pre> tag and 
						user click the submit button (insert code)
					*/
					if (node_pre_onclick.length) 
					{
						if (clean_code == '') {
							node_pre_onclick.remove();
							return;
						}
						
						if (attr_title)
							node_pre_onclick.attr('title', attr_title);
						else
							node_pre_onclick.removeAttr('title');
						node_pre_onclick.attr('class', class_name).html(encoded_html);
						
						if (title)
							node_pre_onclick.attr('title', title);
					} 
					else 
					{
						if (clean_code == '') {
							return;
						}
						ed.insertContent('<pre class="' + class_name + '"' + attr_title + '>' + encoded_html + '</pre><br/>');
						// node_pre_onclick = $(ed.editorContainer).find('iframe').contents().find('pre[class*="aphsh-pretag-focused"]');
					}
					
					node_pre_onclick = '';
					$aphsh_btn.removeClass('mce-active');
								
					$('#aphsh-cancel').trigger('click');
				});
            });
			
			// Aphsh Btn
			function setAphshBtn() {
				if (!$aphsh_btn) {
					$aphsh_btn = $(ed.editorContainer).find("div[class*='aphsh-btn']");
				}
			}
			
			function removeHighlightAphshBtn() {
				if (!aphsh_tag_highlighted) {
					return;
				}
				setAphshBtn();
				$aphsh_btn.removeClass('mce-active');
				aphsh_tag_highlighted = 0;
			}
			
			function highlightAphshBtn()
			{
				setAphshBtn();
				$aphsh_btn.addClass('mce-active');
				aphsh_tag_highlighted = 1;
				pre_tag_highlighted = 1;
			}
			
			// Handle Pre Container in TinyMCE Editor
			function removeHighlightPre()
			{
				if (!pre_tag_highlighted) {
					return;
				}
				
				if (!$iframe_body) {
					$iframe_body = $(ed.editorContainer).find('iframe').contents().find('body');
				}
				$iframe_body.find('pre').removeClass('aphsh-pretag-focused');
				pre_tag_highlighted = 0;
			}
			
			function checkCursor()
			{
				var cursor_node = ed.selection.getNode();
				$cursor_node = $(cursor_node);
				if (cursor_node.nodeName.toLowerCase() == 'pre')
				{
					var classes = $cursor_node.attr('class');
					if (classes.match(/lang\s*:/))
					{
						removeHighlightPre();
						highlightAphshBtn();
						$cursor_node.addClass('aphsh-pretag-focused');
					} else {
						removeHighlightPre();
						removeHighlightAphshBtn();
					}
				} else {
						removeHighlightPre();
						removeHighlightAphshBtn();
				}
			}
			
			// When the text editor is clicked, or cursor moved
			ed.on('click', function(e) {
				checkCursor();
			});
        },
 
        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'APH Syntax Highlighter',
                author : 'Agus Prawoto Hadi',
                authorurl : 'http://www.webdevcorner.com',
                infourl : 'http://www.webdevcorner.com',
                version : "1.0"
            };
        }
    });
	
    // Register plugin
    tinymce.PluginManager.add( 'aphsh_tinymce_btn', tinymce.plugins.Aph_Syntax_Highlighter );
	
})(jQuery);