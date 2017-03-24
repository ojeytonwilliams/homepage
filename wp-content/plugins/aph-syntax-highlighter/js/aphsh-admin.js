(function($) {
	$(document).ready(function(){
		$('#opt-aphsh-lang-pack').change(function()
		{
			var val = $(this).val(),
				lang_list = $.parseJSON($('#aphsh-json-lang-list').text()),
				user_opt = $.parseJSON($('#aphsh-json-user-options').text()),
				options = lang_list[val];
				
			html_option = '';
			$.each(options, function(k, v){
				var selected = k == user_opt['default_lang'] ? ' selected="selected"' : '';
				html_option += '<option value="' + k + '"' + selected + '>' + v + '</option>';
			})
			
			$('#opt-aphsh-default-lang').empty().html(html_option);
		});
		$('#aphsh-defaults').click(function()
		{
			var popup_confirm = confirm('Are you sure want to restore to the default settings?');
			if (popup_confirm == false)
				return false;
		});
		$('#aphsh-add-css-option').change(function()
		{ 
			if ($(this).val() == 1)
			{
				$('#aphsh-add-css-container').fadeIn('fast');
			} else {
				$('#aphsh-add-css-container').fadeOut('fast');
			}
		});
		
		$('#aphsh-css-example-btn').click(function()
		{
			$('#aphsh-css-example').fadeToggle('fast');
			return false;
		});
		var textarea = document.getElementById('aphsh-add-css-textarea');
		tabOverride.set(textarea);
	});
})(jQuery);
