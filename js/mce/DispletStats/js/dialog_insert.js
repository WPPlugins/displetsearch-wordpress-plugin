(function ($, popup) {
	$(function () {
		var plugin = popup.editor.plugins.DispletStats;

		$('#insert_form').submit(function () {
			var $this = $(this);
			var shortcode_name = 'DispletStats';
			var data = $this.serializeArray();
			var data_filtered = [];
			var attrs = '';

			$.each(data, function () {
				if (this.value !== '') {
					attrs += this.name + '="' + this.value + '" ';
					data_filtered.push({'name': this.name, 'value': this.value});
				}
			});

			var index = plugin._searches.push(data_filtered) - 1;

			//console.info('Doing mceInsertHTML from an insert dialog (with a placeholder).');
			popup.editor.execCommand('mceInsertContent',
				false,
				'<p><img id="' + shortcode_name.toLowerCase()
					+ '-' + index + '" src="' + plugin._url
					+ '/images/button_insert_large.png" class="mceItem ' +
					shortcode_name.toLowerCase() + '"/></p>');

			//console.dir($this.serializeArray());
			/*
			tinyMCEPopup.editor.execCommand('mceInsertContent',
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
})(jQuery, tinyMCEPopup)
