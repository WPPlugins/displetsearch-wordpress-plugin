// DispletStats TinyMCE plugin
;(function ($) {
 	tinymce.create('tinymce.plugins.DispletStats', {
		'_searches': [],

		'init': function (editor, url) {
			var that = this;
			this._url = url;

			editor.addCommand('DispletStatsInsert', function () {
				editor.windowManager.open({
					'file': url + '/html/displetstats_insert.html',
					'height': 500,
					'width': 500,
					'inline': 1
				}), {
					'plugin_url': url
				}
			});

			editor.addCommand('DispletStatsEdit', function () {
				editor.windowManager.open({
					'file': url + '/html/displetstats_edit.html',
					'height': 500,
					'width': 500,
					'inline': 1
				}), {
					'plugin_url': url
				};
			});

			editor.addButton('DispletStats', {
				'title': 'Insert DispletStats',
				'cmd': 'DispletStatsInsert',
				'image': url + '/images/button_insert.png'
			});

			// filter shortcode to placeholder html
			// Fires before new contents is added to the editor.
			editor.onBeforeSetContent.add(function (editor, o) {
				
				//console.dir({
				//	'onBeforeSetContent, before _shortcode()': o.content,
				//	'object': o
				//});
				

				o.content = that._shortcode_to_placeholder(o.content);
			});

			// filter placeholder html to shortcode
			// Fires when the Serializer does a postProcess on the contents.
			editor.onPostProcess.add(function (editor, o) {
				if (o.get) {
					
					//console.dir({
					//	'onPostProcess, before _unshortcode()': o.content,
					//	'object': o
					//});

					o.content = that._placeholder_to_shortcode(o.content);
				}

				/*
				if (o.set) {
					//console.dir({
					//	'before _shortcode()': o.content,
					//	'object': o
					//});

					o.content = that._shortcode(o.content);
				}
				*/
			});

			// open the edit dialog
			editor.onMouseDown.add(function (editor, ev) {
				var shortcode = false;;
				var search_index = false;

				var index_regex = /-(\d+)/;

				//var parent_id = ev.target.parentNode.id;
				//var match = index_regex.exec(parent_id);

				if ($(ev.target).hasClass('displetstats')) {
					shortcode = 'displetstats';
				}

				if (shortcode) {
					// experiment: make the placeholder an inline element
					var match = index_regex.exec(ev.target.id);

					if (match !== null) {
						search_index = match[1];
					
						that._current = {
							'type': shortcode,
							'index': search_index,
							'attrs': that._searches[search_index]
						};

						editor.execCommand('DispletStatsEdit');
					}
				}
			});
		},

		// shortcode to placeholder
		'_shortcode_to_placeholder': function (content) {
			var that = this;
			// without the p, it adds &nbsp; for some reason.
			//var shortcode_regex = /(?:<p>)?\[(DispletStats)\s+(.*)\](?:<\/p>)/g;
			var shortcode_regex = /(?:<p>)?\[(DispletStats)\s*(.*)\](?:<\/p>)/g;
			var attr_regex = /(\w+)="([^"]*)"/g;
			
			that._searches = [];
			return content.replace(shortcode_regex, function (shortcode, listing_type, attr_string) {
				var attributes = [];
				var match = [];

				while ((match = attr_regex.exec(attr_string)) !== null) {
					attributes.push({
						'name': match[1],
						'value': match[2]
					});
				};

				var i = that._searches.push(attributes) - 1;

				var class_string = 'displetstats';
				var id_string = 'displetstats-';
				/*
				if (listing_type === 'DispletListing') {
					var id_string = 'displetlisting-';
					var class_string = 'displetlisting';
				}
				else {
					var id_string = 'displethview-';
					var class_string = 'displethview';
				}
				*/
				//return '<div id="' + id_string + i + '"><img src="' + that._url + '/images/button_insert_large.png' + '" class="mceItem ' + class_string + '"/></div>';

				// experiment: use an inline element as the placeholder
				return '<p><img id="' + id_string + i + '" src="' + that._url + '/images/button_insert_large.png' + '" class="mceItem ' + class_string + '"/></p>';
			});
		},

		// placeholder to shortcode
		// this is broken when inserting placeholder first
		'_placeholder_to_shortcode': function (content) {
			var that = this;
			//var listing_regex = /<div\s+id="(displetlisting|displethview)-(\d+)".*>.*<\/div>\s/g;
			// experiment: use an inline element as the placeholder
			// var listing_regex = /<img\s+id="(displetstats)-(\d+)".*>\s/g;

			var stats_regex = /<img id="(displetstats)-(\d+)".*?\/>/g;

			return content.replace(stats_regex, function (div, listing_type, index) {
				var attrs = that._searches[index];
				var attr_string = '';

				$.each(attrs, function (i, v) {
					attr_string += ' ' + this.name + '="' + this.value + '"';
				});

				/*
				if (listing_type === 'displetlisting') {
					var shortcode = 'DispletListing';
				}
				else {
					var shortcode = 'DispletHView';
				}
				*/
				var shortcode = 'DispletStats';
				return '<p>[' + shortcode + attr_string + ']</p>';
			});
		}
	});

	tinymce.PluginManager.add('DispletStats', tinymce.plugins.DispletStats);
 })(jQuery);
