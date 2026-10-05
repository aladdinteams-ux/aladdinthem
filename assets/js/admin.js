/**
 * Larijani Stone – dashboard settings helpers (colour pickers, media picker, confirm buttons).
 */
(function ($) {
	'use strict';
	$(function () {
		$('.ls-admin .ls-color').wpColorPicker();

		$('.ls-admin').on('click', '.ls-media-pick', function (e) {
			e.preventDefault();
			var wrap = $(this).closest('.ls-media');
			var frame = wp.media({ title: $(this).text(), multiple: false, library: { type: 'image' } });
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				wrap.find('input[type=url]').val(att.url);
				wrap.find('.ls-media-preview').html($('<img>', { src: att.url, alt: '' }));
			});
			frame.open();
		});

		$('.ls-admin').on('click', '[data-ls-confirm]', function (e) {
			if (!window.confirm($(this).attr('data-ls-confirm'))) {
				e.preventDefault();
			}
		});
	});
})(jQuery);
