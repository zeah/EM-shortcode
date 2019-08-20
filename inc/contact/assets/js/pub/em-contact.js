jQuery(function($) {

	$('.em-contact-input').focus(function() {
		$(this).css('background-color', 'white');
	});

	$('.em-contact-title-slide').click(function() {
		$('.em-contact-slide').slideToggle();
	});

	$('.em-contact-button').click(function() {

		var email = $('.em-contact-email').val();
		var message = $('.em-contact-message').val();
		var name = $('.em-contact-name').val();

		var stop = false
		var error = function(n) { $(n).css('background-color', '#fee'); stop = true; }

		if (!/.{3,}/.test(name)) error('.em-contact-name');
		if (!/.+\@.+\..{2,}/.test(email)) error('.em-contact-email');
		if (!/.{5,}/.test(message)) error('.em-contact-message');

		if (stop) return;

		$.post(emurl.ajax_url, {
			action: 'con',
			side: location.href,
			name: name,
			email: email,
			message: message

		}, function(data) {

			if (data == '1') {
				$('.em-contact-name').val('');
				$('.em-contact-email').val('');
				$('.em-contact-message').val('');
				$('.em-contact-thanks').slideDown(500);
				$('.em-contact-title').slideUp(500);
				$('.em-contact-part').slideUp(500);

			}
		});
	});

});