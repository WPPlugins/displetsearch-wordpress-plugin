;(function ($, popup) {
	$(function () {
		var plugin = popup.editor.plugins.DispletStats;
		var $form = $('#edit_form');

		$.each(plugin._current.attrs, function (i, v) {
			//$('[name=' + this.name + ']', $form).val(this.value);
			var $el = $('[name=' + this.name + ']', $form);

			if ($el.attr('type') === 'text') {
				$el.val(this.value);
			}
			else {
				$el.val([this.value]);
			}
		});

		$form.submit(function () {
			var $this = $(this);
			var shortcode_name = 'DispletStats';
			var data = $this.serializeArray();
			var data_filtered = [];
			var attrs = '';

			$.each(data, function () {
				if (this.value !== '') {
					data_filtered.push({'name': this.name, 'value': this.value});
					attrs += this.name + '="' + this.value + '" ';
				}
			});

			plugin._searches[plugin._current.index] = data_filtered;

			var index = plugin._current.index;

			//console.info('Doing mceInsertHTML from an insert dialog (with a placeholder).');
			popup.editor.execCommand('mceInsertContent',
				false,
				'<img id="' + shortcode_name.toLowerCase()
					+ '-' + index + '" src="' + plugin._url
					+ '/images/button_insert_large.png" class="mceItem ' +
					shortcode_name.toLowerCase() + '"/>');
			popup.editor.execCommand('mceRepaint');

			/*
			popup.editor.execCommand('mceInsertContent',
				false,
				'[' + shortcode_name + ' ' + attrs + ']');
			*/

			popup.close();
		});

		$('button#cancel').click(function () {
			popup.close();
		});

		$('.ui-tabs').tabs();
	});
})(jQuery, tinyMCEPopup);
