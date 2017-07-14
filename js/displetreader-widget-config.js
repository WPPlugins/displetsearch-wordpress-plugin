;(function ($) {
	// widget to attach to all div.displet-widget-configure
	$.widget('displetreader.widget_config', {
		'options': {
			'dialog_options': {
				'minWidth': 500,
				'minHeight': 500,
				'buttons': [{
					'text': 'Ok',
					'click': function () {
						var $form = $('form', this);
						$form.trigger('submit');
						$(this).dialog('close');
					}
				}],
				'autoOpen': false,
				'create': function () {
					// hack so css scope works
					$(this).closest('.ui-dialog').wrap('<div class="displetreader"/>');
				}
			},
			'markup_selector': '.displet-widget-control-markup',
			'tabs_selector': '.ui-tabs',
			'config_button_selector': 'button.displet-widget-configure',
			'options_input_selector': '.displet-widget-control-settings'
		},

		'_create': function () {
			var $this = $(this);
			var that = this;
			var o = this.options;
			var el = this.element;

			this.options_input = $(o.options_input_selector, el);
			this.wp_widget_form = $(el).closest('.widget-inside').find('form');

			this.$dialog = $(this._decode_html()).dialog(o.dialog_options);

			this.form = $('form', this.$dialog);

			$(o.tabs_selector, this.$dialog).tabs();

			$(o.config_button_selector, el).bind('click', function (ev) {
				that.$dialog.dialog('open');
				ev.preventDefault();
				return false;
			});

			this.form.bind('submit', function (ev) {
				that.save_settings($(this).serializeArray());
				ev.preventDefault();
				ev.stopPropagation();
				return false;
			});

			this._find_title();
		},

		'_init': function () {
			this.wp_widget_settings = this.options_input.val();
			this.populate();
			this._find_title();
		},

		'_find_title': function () {
			var title = $(this.element).closest('.widget').find('h4').html();
			if (title !== null) {
				this.$dialog.dialog('option', 'title', title);
			}
		},

		'_decode_html': function () {
			var encoded_html = $(this.options.markup_selector, this.element).html();
			var decoded_html = $('<div class="displetreader" />').html(encoded_html).text();
			return decoded_html;
		},

		'populate': function () {
			var that = this;

			if (this.wp_widget_settings != '') {
				var data = this.parse_uri(this.wp_widget_settings);

				$.each(data, function (i, v) {
					$(':input[name="' + this.name + '"]', that.form).val(this.value);
				});
			}
		},

		'parse_uri': function (uri) {
			var array = uri.split('&');
			var data = [];
			$.each(array, function (i, v) {
				var input = v.split('=');
				data.push({
					'name': unescape(input[0]),
					'value': unescape(input[1])
				});
			});

			return data;
		},

		'save_settings': function (data) {
			var filtered = [];

			$.each(data, function (i, v) {
				if (this.value !== '') {
					filtered.push({
						'name': this.name,
						'value': this.value
					});
				}
			});
			this.options_input.val($.param(filtered));

			$('input.widget-control-save', this.wp_widget_form).click();
		},

		'destroy': function () {
			$.Widget.prototype.destroy.apply(this, arguments);
			this.$dialog.dialog('destroy');
		}
	});

	// document.ready, attach
	$(function () {
		var widgets_right = $('div#widgets-right');

		// attach widget instances on page load
		$('div.displet-widget-control', widgets_right).widget_config();

		// listen for dragstop on ui-draggables
		$('.ui-draggable').live('dragstop', function (ev, ui) {
			$('div.displet-widget-control', widgets_right).widget_config();
		});

		// wp replaces node on submit, listen for it
		$(widgets_right).ajaxStop(function () {
			$('div.displet-widget-control', widgets_right).widget_config();
		});
	});
})(jQuery);
