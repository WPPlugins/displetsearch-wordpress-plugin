// DispletListing TinyMCE plugin
;(function ($) {
 	tinymce.create('tinymce.plugins.DispletListing', {
		'_searches': [],

		'_get_listing_defaults': function () {
			var that = this;
			$.ajax({
				'async': false,
				'url': ajaxurl,
				'data': {
					'action': 'displetreader',
					'subaction': 'get_listing_defaults',
					'method': 'get',
				},
				'success': function (json) {
					that._listing_defaults = json;
					return json;
				}
			});
		},

		'init': function (editor, url) {
			var that = this;
			this._url = url;
			this._listing_defaults = false;

			//this._listing_defaults = this._get_listing_defaults();

			this._get_listing_defaults();

			//console.dir(this._listing_defaults);
			// event debugging
			/*
			editor.onBeforeGetContent.add(function (ed, o) {
				//console.info('Inside onBeforeGetContent callback.');
				//console.dir({
				//	'object': o
				//});
			});

			editor.onChange.add(function (ed, o) {
				//console.info('Inside onChange callback.');
				//console.dir({
				//	'object': o
				//});
			});

			editor.onGetContent.add(function (ed, o) {
				//console.info('Inside onGetContent callback.');
				//console.dir({
				//	'object': o
				//});
			});

			editor.onPostRender.add(function (ed, o) {
				//console.info('Inside onPostRender callback.');
				//console.dir({
				//	'object': o
				//});
			});

			editor.onSetContent.add(function (ed, o) {
				//console.info('Inside onSetContent callback.');
				//console.dir({
				//	'object': o
				//});
			});

			editor.onPreProcess.add(function (ed, o) {
				//console.info('Inside onPreProcess callback.');
				//console.dir({
				//	'object': o
				//});				
			});
			*/

			editor.addCommand('DispletListingInsert', function () {
				editor.windowManager.open({
					'file': url + '/html/displetlisting_insert.html',
					'height': 530,
					'width': 500,
					'inline': 1
				}), {
					'plugin_url': url,
					'default_sort': 'sort',
					'default_orientation': 'orientation'
				}
			});

			editor.addCommand('DispletListingEdit', function () {
				editor.windowManager.open({
					'file': url + '/html/displetlisting_edit.html',
					'height': 530,
					'width': 500,
					'inline': 1
				}), {
					'plugin_url': url
				};
			});

			editor.addButton('DispletListing', {
				'title': 'Insert DispletListing',
				'cmd': 'DispletListingInsert',
				'image': url + '/images/button_insert.png'
			});

			// filter shortcode to placeholder html
			// Fires before new contents is added to the editor.
			
			editor.onBeforeSetContent.add(function (editor, o) {
				//console.info('Inside onBeforeSetContent callback.');
				//console.dir({
				//	'object': o
				//});
				o.content = that._shortcode_to_placeholder(o.content);
			});
			

			// filter placeholder html to shortcode
			// Fires when the Serializer does a postProcess on the contents.
			// bug here: placeholder not being transformed.
			editor.onPostProcess.add(function (editor, o) {
				//console.info('Inside onPostProcess callback');
				//console.dir({
				//	'object': o
				//});

				if (o.get) {
					/*
					//console.dir({
					//	'onPostProcess, before _unshortcode()': o.content,
					//	'object': o
					//});
					*/

					o.content = that._placeholder_to_shortcode(o.content);
				}
				else if (o.set) {
					// translate tiny_mce_marker to placeholder
					//o.content = that._marker_to_placeholder();
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
				var shortcode = false;
				var search_index = false;

				if ($(ev.target).hasClass('displethview')) {
					shortcode = 'displethview';
				}
				else if ($(ev.target).hasClass('displetlisting')) {
					shortcode = 'displetlisting';
				}

				if (shortcode) {
					var index_regex = /-(\d+)/;

					//var parent_id = ev.target.parentNode.id;
					//var match = index_regex.exec(parent_id);

					// experiment: make the placeholder an inline element
					var match = index_regex.exec(ev.target.id);

					if (match !== null) {
						search_index = match[1];
					
						that._current = {
							'type': shortcode,
							'index': search_index,
							'attrs': that._searches[search_index]
						};

						editor.execCommand('DispletListingEdit');
					}
				}
			});
		},

		// shortcode to placeholder
		'_shortcode_to_placeholder': function (content) {
			//console.info('Inside _shortcode_to_placeholder(), onBeforeSetContent');
			//console.dir({'content': content});
			var that = this;
			// without the p, it adds &nbsp; for some reason.
			var shortcode_regex = /(?:<p>)?\[(DispletListing|DispletHView)\s*(.*)\](?:<\/p>)/g;
			//var shortcode_regex = /\[(DispletListing|DispletHView)\s+(.*)\]/g;
			
			//var attr_regex = /(\w+)="(\w+)"/g;

			var attr_regex = /(\w+)="([^"]*)"/g;

			that._searches = [];

			//console.dir({'regex matches': content.match(shortcode_regex)});

			return content.replace(shortcode_regex, function (shortcode, listing_type, attr_string) {
				//console.dir({'captured': {
				//	'whole_match': shortcode,
				//	'capture 1 (type)': listing_type,
				//	'capture 2 (attribute string)': attr_string
				//}});
				var attributes = [];
				var match = [];

				while ((match = attr_regex.exec(attr_string)) !== null) {
					attributes.push({
						'name': match[1],
						'value': match[2]
					});
				};

				var i = that._searches.push(attributes) - 1;

				if (listing_type === 'DispletListing') {
					var id_string = 'displetlisting-';
					var class_string = 'displetlisting';
				}
				else {
					var id_string = 'displethview-';
					var class_string = 'displethview';
				}

				//return '<div id="' + id_string + i + '"><img src="' + that._url + '/images/button_insert_large.png' + '" class="mceItem ' + class_string + '"/></div>';

				var return_string = '<p><img id="' + id_string + i + '" src="' + that._url + '/images/button_insert_large.png' + '" class="mceItem ' + class_string + '"/></p>';

				//console.dir({'output_content': return_string});
				return return_string;

				// experiment: use an inline element as the placeholder
				//return '<p><img id="' + id_string + i + '" src="' + that._url + '/images/button_insert_large.png' + '" class="mceItem ' + class_string + '"/></p>';
			});
		},

		// placeholder to shortcode
		// this is broken when inserting placeholder first
		// bug here: placeholder not being transformed
		'_placeholder_to_shortcode': function (content) {
			//console.info('Inside _placeholder_to_shortcode(), onPostProcess');
			//console.dir({'content': content});

			var that = this;
			//var listing_regex = /<div\s+id="(displetlisting|displethview)-(\d+)".*>.*<\/div>\s/g;
			// experiment: use an inline element as the placeholder
			//var listing_regex = /<p><img\s+id="(displetlisting|displethview)-(\d+)".*>.*<\/p>/g;
			//var listing_regex = /<img id="(displetlisting|displethview)-(\d+)".*?\/>/g;
			//var listing_regex = /<img class="mceItem displetlisting" id="(displetlisting|displethview)-(\d+)".*?\/>/g;
			var listing_regex = /<img[\A class="mceItem displetlisting"\Z]* id="(displetlisting|displethview)-(\d+)".*?\/>/g;
			//console.dir({'regex matches': content.match(listing_regex)});
	
			return content.replace(listing_regex, function (div, listing_type, index) {
				//console.info('Replacing placeholders with shortcodes.');
				//console.dir({'captured': {
				//	'whole_match': div,
				//	'capture 1 (type)': listing_type,
				//	'capture 2 (index)': index
				//}});

				var attrs = that._searches[index];
				var attr_string = '';

				$.each(attrs, function (i, v) {
					attr_string += ' ' + this.name + '="' + this.value + '"';
				});

				if (listing_type === 'displetlisting') {
					var shortcode = 'DispletListing';
				}
				else {
					var shortcode = 'DispletHView';
				}

				var return_string = '<p>[' + shortcode + attr_string + ']</p>';

				//console.dir({'output_content': return_string});
				return return_string;
			});
		}
	});

	tinymce.PluginManager.add('DispletListing', tinymce.plugins.DispletListing);
 })(jQuery);
