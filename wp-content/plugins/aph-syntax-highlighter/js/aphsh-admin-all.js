(function($) {
	$(document).ready(function()
	{
		$('div.aphsh-always-display-notice').delegate('button.notice-dismiss', 'click', function() {
			var msg = $.trim($(this).prev().text());
			$.ajax({
				type: 'POST',
				url: aphsh.ajaxurl,
				dataType: 'text',
				data: {
					action: 'aphsh-dismiss-notice',
					nonce: aphsh.nonce,
					msg: msg
				},
				success: function(data) {
					// console.log(data);

				},
				error: function(error) {
					// console.log('error');
				}
			});
		});
	});
})(jQuery);
