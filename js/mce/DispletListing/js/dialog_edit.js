;(function ($, popup) {
	$(function () {
		var plugin = popup.editor.plugins.DispletListing;
		var $form = $('#edit_form');
		var type = plugin._current.type;
		var orientation = false;
		var order;
		var $orientation = $(':input[name=orientation]', $form);

		$.each(plugin._current.attrs, function (i, v) {
			if (this.name === 'order') {
				order = this.value;
			}
		});

		// populate the form from shortcode attributes
		$.each(plugin._current.attrs, function (i, v) {
			switch (this.name) {
			case 'sort_by':
				if (this.value === 'list-price') {
					if (order === 'desc') {
						$('select[name=sortby]', $form).val('price_high_to_low');
					}
					else {
						$('select[name=sortby]', $form).val('price_low_to_high');
					}
				}
				else if (this.value === 'list-date') {
					if (order === 'desc') {
						$('select[name=sortby]', $form).val('list_date_desc');
					}
				}
				break;

			case 'order':
				
				break;

			case 'orientation':
				if (type !== 'displethview') {
					orientation = this.value;
	
					$orientation.each(function () {
						if ($(this).val() == orientation) {
							$(this).attr('checked', 'checked');
						}
					});
				}
				break;
				
			

			default:
				var $el = $('[name=' + this.name + ']', $form);

				if ($el.attr('type') === 'text') {
					$el.val(this.value);
				}
				else {
					$el.val([this.value]);
				}
			}
		});

		if (type === 'displethview') {
			$orientation.filter('[value=horizontal]').attr('checked', 'checked');
		}

		// for backwards compatibility
		if (type === 'displetlisting' && !orientation) {
			$orientation.filter('[value=vertical]').attr('checked', 'checked');
		}
/*
			if (this.name === 'sort_by') {
				if (this.value === 'list-price') {
					if (order === 'desc') {
						$('select[name=sortby]', $form).val('price_high_to_low');
					}
					else {
						$('select[name=sortby]', $form).val('price_low_to_high');
					}
				}
			}
			else if (this.name !== 'order') {
				$('[name=' + this.name + ']', $form).val(this.value);
			}

		});

		//console.log(plugin._current);
		var $orientation = $(':input[name=orientation]', $form);
		//console.info($orientation.val());
		if (type === 'displethview') {
			orientation = 'horizontal';
		}
		else if ($orientation.val() === 'tile') {
			orientation = 'tile';
		}
		else {
			orientation = 'vertical';
		}

		// workaround for jquery bug
		$orientation.each(function () {
			if ($(this).val() == orientation) {
				$(this).attr('checked', 'checked');
			}
		});
*/
		$form.submit(function () {
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
						data_filtered.push({'name': this.name, 'value': this.value});
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
					attrs += this.name + '="' + this.value + '" ';

					data_filtered.push({'name': this.name, 'value': this.value});
				}
			});

			//console.info(shortcode_name);

			plugin._searches[plugin._current.index] = data_filtered;

			//console.info('Doing mceInsertRawHTML from an insert dialog.');

			//console.dir(plugin._current);
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
			popup.editor.execCommand('mceRepaint');
			*/
			popup.close();
		});

		$('button#cancel').click(function () {
			popup.close();
		});

		$('.ui-tabs').tabs();
	});
})(jQuery, tinyMCEPopup);
