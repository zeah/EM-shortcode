jQuery(function($) {

	var send = function(button, id, postid) {
		$.post(emurl.ajax_url, {
			action: 'sett',
			security: emurl.sec,
			id: id,
			postid: postid,
			button: button
		}, function(data) {
			console.log(data);
		});
	}

	$('.em-rating-button').click(function() {
		var val = $(this).attr('data-val');
		var id = $(this).parent().attr('data-id');
		var postid = $(this).parent().parent().attr('data-id');
		if (val && id && postid) send(val, id, postid);

		switch (val) {
			case 'delete': $(this).parent().parent().toggle(500); break;
			case 'hide': $(this).parent().parent().find('.em-rating-status').text('hidden'); break;
			case 'approve': $(this).parent().parent().find('.em-rating-status').text('approve'); break;
		}

	});

});