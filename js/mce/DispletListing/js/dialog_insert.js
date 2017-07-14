;(function ($, popup) {
	$(function () {
		var plugin = popup.editor.plugins.DispletListing;
		var $form = $('form');
		var listing_defaults = plugin._listing_defaults;

		if (listing_defaults !== false) {
			var $orientation = $(':input[name=orientation]', $form);

			// workaround for jquery bug
			$orientation.each(function () {
				if ($(this).val() == listing_defaults.default_orientation) {
					$(this).attr('checked', 'checked');
				}
			});

			/*
			var $sort = $('select[name=sortby]', $form);
			$sort.val(listing_defaults.default_sort);
			*/
		}

		$('#insert_form').submit(function () {
			var $this = $(this);
			var shortcode_name = 'DispletListing';
			var data = $this.serializeArray();
			var data_filtered = [];
			var attrs = '';

			$.each(data, function () {
				if (this.name === 'orientation') {
					if (this.value === 'horizontal') {
						shortcode_name = 'DispletHView';
					}
					else {
						data_filtered.push({
							'name': this.name,
							'value': this.value
						});
					}
				}
				else if (this.name === 'sortby') {
					if (this.value === 'price_high_to_low') {
						data_filtered.push({
							'name': 'sort_by',
							'value': 'list-price'
						});
						
						data_filtered.push({
							'name': 'order',
							'value': 'desc'
						});
					}
					else if (this.value === 'price_low_to_high') {
						data_filtered.push({
							'name': 'sort_by',
							'value': 'list-price'
						});
						
						data_filtered.push({
							'name': 'order',
							'value': 'asc'
						});
					}
					else if (this.value === 'list_date_desc') {
						data_filtered.push({
							'name': 'sort_by',
							'value': 'list-date'
						});
						
						data_filtered.push({
							'name': 'order',
							'value': 'desc'
						});
					}
				}
				else if (this.value !== '') {
					attrs += ' ' + this.name + '="' + this.value + '"';

					data_filtered.push({'name': this.name, 'value': this.value});
				}
			});

			// idea: push search data on to _searches array, draw html with placeholder.

			var index = plugin._searches.push(data_filtered) - 1;

			//console.info('Doing mceInsertHTML from an insert dialog (with a placeholder).');
			
			popup.editor.execCommand('mceInsertContent',
				false,
				'<p><img id="' + shortcode_name.toLowerCase()
					+ '-' + index + '" src="' + plugin._url
					+ '/images/button_insert_large.png" class="mceItem ' +
					shortcode_name.toLowerCase() + '"/></p>');
			

			/*
			//console.info('Doing mceInsertHTML from an insert dialog.');
			popup.editor.execCommand('mceInsertContent',
				false,
				'[' + shortcode_name + ' ' + attrs + ']');
			popup.editor.execCommand('mceRepaint');
			*/

			/*
			//console.info('Doing mceInsertRawHTML from an insert dialog.');
			//console.dir({'content to insert': '[' + shortcode_name + ' ' + attrs + ']'});
			popup.editor.execCommand('mceInsertRawHTML',
				false,
				'[' + shortcode_name + ' ' + attrs + ']');
			popup.editor.focus();
			*/
			popup.close();
		});

		$('button#cancel').click(function () {
			popup.close();
		});

		$('.ui-tabs').tabs();
	});
})(jQuery, tinyMCEPopup);
