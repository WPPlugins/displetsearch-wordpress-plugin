/*********************
Gallery Listings JS
*********************/

;(function ($) {
	$.widget('displetreader.displet_tile', {
		'options': {
			'first_selector': 'span.displet-tile-counts-first',
			'last_selector': 'span.displet-tile-counts-last',
			'total_selector': 'span.displet-tile-counts-total',
			'previous_selector': 'a.displet-tile-control-prev-link',
			'next_selector': 'a.displet-tile-control-next-link',
			'sortby_selector': '.displet-tile-sortby',
			'listing_selector': '.displet-tile-listing',
			'selector_map': {
				'list-price': '.displet-tile-listing-price',
				'list-date': '.displet-tile-list-date'
			},
		},

		'_create': function () {
			var el = this.element;
			var o = this.options;
			var self = this;

			this.active_listings = new Array();
			this.first_count_el = $(o.first_selector, el);
			this.last_count_el = $(o.last_selector, el);
			this.total_count_el = $(o.total_selector, el);
			this.prev_button = $(o.previous_selector, el);
			this.next_button = $(o.next_selector, el);
			this.listings = $(o.listing_selector, el);
			this.listings_parent = this.listings.parent();
			this.sortby_select = $(o.sortby_selector, el);

			this.first_index = Number($(this.first_count_el[0]).html()) - 1;
			this.last_index = Number($(this.last_count_el[0]).html()) - 1;
			this.total_count = Number($(this.total_count_el[0]).html());

			if (this.total_count > 0) {
				this.rpp = this.last_index + 1;

				if ($.cookie('displet_last_viewed_listings_page')) {
					this.current_page = parseInt($.cookie('displet_last_viewed_listings_page'));
				}
				else {
					this.current_page = 1;	
				}

				$(this.sortby_select, el).bind('change', function (ev) {
					if (this.value) {
						self.sort(this.value);
						$.cookie('displet_last_viewed_listings_sort', this.value, {path: '/'});
					}
				});

				$(this.next_button).click(function (ev) {
					self.next_page();
					ev.preventDefault();
					return false;
				});

				$(this.prev_button).click(function (ev) {
					self.prev_page();
					ev.preventDefault();
					return false;
				});

				this._size_listings();

				this._adjust_rpp();

				this._clear_float();

				this._make_pages();

				if ($('#displet-tile').hasClass('price_low_to_high')) {
					this.current_sort = 'list-price-asc';
				}
				else if ($('#displet-tile').hasClass('price_high_to_low')) {
					this.current_sort = 'list-price-desc';
				}
				var sort_cookie_value = $.cookie('displet_last_viewed_listings_sort', {path: '/'});
				if (sort_cookie_value && sort_cookie_value != this.current_sort) {
					self.sort_onsamepage(sort_cookie_value);
				}

				this.listings.hide();

				this.goto_page(this.current_page);
			}
			else {
				this.first_count_el.html('0');
				this.last_count_el.html('0');
				this.total_count_el.html('0');
			}

			setTimeout(function(){
				self.listings_parent.css('min-height', self.listings_parent.height());
			}, 400);

		},

		'_adjust_rpp': function () {
			if (this.listings_per_row != 0) {
				while (this.rpp % this.listings_per_row != 0) {
					this.rpp++;
				}
			}
		},

		'display_this_view': function () {
			this._size_listings();
			this._adjust_rpp();
			this._clear_float();
			this._make_pages();
			var last_viewed_sort = $('#displet-listings').data('displet_listings').last_viewed_sort();
			if (last_viewed_sort && last_viewed_sort != this.current_sort) {
				this.sort_onsamepage(last_viewed_sort);
			}
			var last_viewed_page = $('#displet-listings').data('displet_listings').last_viewed_page();
			if (last_viewed_page) {
				this.current_page = last_viewed_page;
			}
			else{
				this.current_page = 1;
			}
			$('#displet-listings').fadeOut(400);
			this.goto_page(this.current_page, true);
			$.cookie('displet_last_viewed_listings_orientation', 'tile', {path: '/'});
		},

		'last_viewed_page': function () {
			return this.current_page;
		},

		'last_viewed_sort': function () {
			return this.current_sort;
		},

		'show_prices': function (minprice, maxprice) {
			this.listings.fadeOut(200);
			var i = 0;
			var total_price = 0;
			var total_square_feet = 0;
			var total_actives = 0;
			var lowest_price = 999999999;
			var highest_price = 0;
			var lowest_square_feet = 999999999;
			var highest_square_feet = 0;
			$('#displet-tile .displet-tile-listing').each(function () {
				var listing_price = parseInt($(this).children('.displet-tile-price-container').children('.displet-tile-listing-price').text().replace(/(\W*)/g, ''));
				if (listing_price >= minprice && listing_price <= maxprice) {
					i++;
					$(this).addClass('displet-in-price-range');
					total_price += listing_price;
					if (listing_price > highest_price) {
						highest_price = listing_price;
					}
					if (listing_price < lowest_price) {
						lowest_price = listing_price;
					}
					var square_feet = parseInt($(this).children('.displet-tile-info').children('.displet-tile-specs').children('.displet-square-feet').children('.displet-square-feet-value').text().replace(/(\W*)/g, ''));
					if (square_feet > 0) {
						total_square_feet += square_feet;
						if (square_feet > highest_square_feet) {
							highest_square_feet = square_feet;
						}
						if (square_feet < lowest_square_feet) {
							lowest_square_feet = square_feet;
						}
					}
					if (!$(this).children('.displet-tile-thumb-container').children('div').hasClass('tileoverlay')) {
						total_actives++;
					}
				}
				else {
					$(this).hide();
					if ($(this).hasClass('displet-in-price-range')) {
						$(this).removeClass('displet-in-price-range');
					}	
				}
			});
			this.total_count = i;
			this.current_prices = total_price;
			this.current_square_footages = total_square_feet;
			this.current_actives = total_actives;
			this.current_highest_price = highest_price;
			this.current_lowest_price = lowest_price;
			this.current_highest_square_feet = highest_square_feet;
			this.current_lowest_square_feet = lowest_square_feet;
			$('#displet-tile span.displet-tile-counts-total').text(this.total_count);
			$('#displet-tile').displet_tile('option', {'listing_selector': '.displet-tile-listing.displet-in-price-range'});

			var el = this.element;
			var o = this.options;
			var self = this;

			this.first_count_el = $(o.first_selector, el);
			this.last_count_el = $(o.last_selector, el);
			this.listings = $(o.listing_selector, el);

			this.first_index = Number($(this.first_count_el[0]).html()) - 1;
			this.last_index = Number($(this.last_count_el[0]).html()) - 1;

			if (this.total_count > 0) {
				this.current_page = 1;
				this._size_listings();
				this._clear_float();
				this._make_pages();
				this.goto_page(this.current_page);
			}
			else {
				this.first_count_el.html('0');
				this.last_count_el.html('0');
				this.total_count_el.html('0');
			}

			setTimeout(function(){
				self.listings_parent.css('min-height', self.listings_parent.height());
			}, 400);
		},
		
		'total_price': function () {
			return this.current_prices;
		},
		
		'highest_price': function () {
			return this.current_highest_price;
		},
		
		'lowest_price': function () {
			return this.current_lowest_price;
		},
		
		'total_square_feet': function () {
			return this.current_square_footages;
		},
		
		'highest_square_feet': function () {
			return this.current_highest_square_feet;
		},
		
		'lowest_square_feet': function () {
			return this.current_lowest_square_feet;
		},
		
		'total_listings': function () {
			return this.total_count;
		},
		
		'total_actives': function () {
			return this.current_actives;
		},

		'_size_listings': function () {
			var listings_per_row = 0;
			var target_width = 0;
			var margin_total = 0;
			var margin = 0;

			this.listing_width = $(this.listings[0]).width() + 12;
			this.listings_parent_width = this.listings_parent.width();

			if (this.listing_width != 0) {
				this.listings_per_row = Math.floor(this.listings_parent_width / this.listing_width);
				target_width = Math.floor(this.listings_parent_width / this.listings_per_row);
				margin_total = target_width - this.listing_width;
				margin = Math.floor(margin_total / 2);

				this.listings.css({
					'margin-left': margin,
					'margin-right': margin
				});
			}
		},
		
		'_clear_float': function () {
			var i = 1;
			var rowlength = this.listings_per_row;
			$(this.listings).each(function(){
				$(this).removeClass('displet-clear');
				if (i % rowlength == 1) {
					$(this).addClass('displet-clear');
				}
				i++;
			});
		},
		
		'_hovertrans': function () {
			$.each(this.active_listings, function(){
				$(this).hover(
					function () {
						$(this).children('.displet-tile-hovertrans').stop(true, true).fadeIn(200);
					},
					function () {
						$(this).children('.displet-tile-hovertrans').stop(true, true).fadeOut(200);
					}
				);
			});
		},

		'_make_pages': function () {
			this.pages = {};

			var pages_count = Math.ceil(this.total_count / this.rpp);
			this.pages_count = pages_count;

			var page = 1;
			this.pages[page] = [];

			for (var i = 0; i < this.total_count; i++) {
				if (i < (page * this.rpp)) {
					this.pages[page].push(i);
				}
				else {
					page++;
					this.pages[page] = [];
					this.pages[page].push(i);
				}
			}
		},

		'_update_counts': function (first, last) {
			this.first_count_el.html(first);
			this.last_count_el.html(last);
		},

		'next_page': function () {
			this.goto_page(this.current_page + 1);
		},

		'prev_page': function () {
			this.goto_page(this.current_page - 1);
		},

		'goto_page': function(page, from_other_view) {
			if(typeof(from_other_view) === 'undefined') {
				from_other_view = false;
			}
			if (from_other_view && page > this.pages_count) {
				page = this.pages_count;
			}
			if ((typeof this.pages[page]) != 'undefined') {
				var self = this;

				if (this.active_listings.length && this.current_page != page) {
					$.each(this.active_listings, function(){
						$(this).fadeOut(400);
					});
				}
				else if (this.active_listings.length){
					$.each(this.active_listings, function(){
						$(this).hide();
					});
				}
				this.active_listings = new Array();

				var j = -1;
				$.each(this.pages[page], function (i,v) {
					var img = $(self.listings[v]).children('.displet-tile-thumb-container').children('img');
					$(img).attr('src', $(img).attr('value'));
					$(self.listings[v]).fadeIn(400);
					var hovertrans = $(self.listings[v]).children('.displet-tile-hovertrans');
					$(hovertrans).children('.displet-inner').children('.displet-inner2').height($(hovertrans).parent('.displet-tile-listing').height() - 8);
					$(hovertrans).width($(hovertrans).parent('.displet-tile-listing').width() + 10);
					self.active_listings.push($(self.listings[v]));
					j++;
				});
	
				this._update_counts(this.pages[page][0] + 1, this.pages[page][j] + 1);
				this.current_page = page;
				this._hovertrans();
				$.cookie('displet_last_viewed_listings_page', page);
			}
		},

		'_sort': function (field, dir) {
			this.listings = $('.displet-tile-listing', this.element);
			var self = this;

			var filter_regex = /(\W*)/g;
			var digits_regex = /\D*/g;
			var listings_array = this.listings.toArray();
			var map = this.options.selector_map;

			this.listings_parent.empty();

			listings_array.sort(function (a, b) {
				var a_val = $.trim($(map[field], a).html()).replace(filter_regex, '');
				var b_val = $.trim($(map[field], b).html()).replace(filter_regex, '');

				if (digits_regex.test(a_val)) {
					a_val = new Number(a_val);
					b_val = new Number(b_val);
				}

				if (dir === 'asc') {
					if (a_val > b_val) {
						return 1;
					}
					else if (a_val === b_val) {
						return 0;
					}
					else {
						return -1;
					}
				}
				else if (dir === 'desc') {
					if (a_val < b_val) {
						return 1;
					}
					else if (a_val === b_val) {
						return 0;
					}
					else {
						return -1;
					}
				}
			});

			$.each(listings_array, function () {
				self.listings_parent.append(this);
			});

			this.listings = $(this.options.listing_selector, this.element);
			this._clear_float();
		},

		'sort_property_type': function (property_type, property_type2) {
			this.listings.fadeOut(200);
			var i = 0;
			var total_price = 0;
			var total_square_feet = 0;
			var total_actives = 0;
			var lowest_price = 999999999;
			var highest_price = 0;
			var lowest_square_feet = 999999999;
			var highest_square_feet = 0;
			$('#displet-tile .displet-tile-listing').each(function () {
				var listing_price = parseInt($(this).children('.displet-tile-price-container').children('.displet-tile-listing-price').text().replace(/(\W*)/g, ''));
				var property_type_value = $(this).find('.displet-property-type').text().toLowerCase();
				if (property_type_value.indexOf(property_type) !== -1 || property_type_value.indexOf(property_type2) !== -1) {
					i++;
					$(this).addClass('displet-in-price-range');
					total_price += listing_price;
					if (listing_price > highest_price) {
						highest_price = listing_price;
					}
					if (listing_price < lowest_price) {
						lowest_price = listing_price;
					}
					var square_feet = parseInt($(this).children('.displet-tile-info').children('.displet-tile-specs').children('.displet-square-feet').children('.displet-square-feet-value').text().replace(/(\W*)/g, ''));
					if (square_feet > 0) {
						total_square_feet += square_feet;
						if (square_feet > highest_square_feet) {
							highest_square_feet = square_feet;
						}
						if (square_feet < lowest_square_feet) {
							lowest_square_feet = square_feet;
						}
					}
					if (!$(this).children('.displet-tile-thumb-container').children('div').hasClass('tileoverlay')) {
						total_actives++;
					}
				}
				else {
					$(this).hide();
					if ($(this).hasClass('displet-in-price-range')) {
						$(this).removeClass('displet-in-price-range');
					}	
				}
			});
			this.total_count = i;
			this.current_prices = total_price;
			this.current_square_footages = total_square_feet;
			this.current_actives = total_actives;
			this.current_highest_price = highest_price;
			this.current_lowest_price = lowest_price;
			this.current_highest_square_feet = highest_square_feet;
			this.current_lowest_square_feet = lowest_square_feet;
			$('#displet-tile span.displet-tile-counts-total').text(this.total_count);
			$('#displet-tile').displet_tile('option', {'listing_selector': '.displet-tile-listing.displet-in-price-range'});

			var el = this.element;
			var o = this.options;
			var self = this;

			this.first_count_el = $(o.first_selector, el);
			this.last_count_el = $(o.last_selector, el);
			this.listings = $(o.listing_selector, el);

			this.first_index = Number($(this.first_count_el[0]).html()) - 1;
			this.last_index = Number($(this.last_count_el[0]).html()) - 1;

			if (this.total_count > 0) {
				this.current_page = 1;
				this._size_listings();
				this._clear_float();
				this._make_pages();
				this.goto_page(this.current_page);
			}
			else {
				this.first_count_el.html('0');
				this.last_count_el.html('0');
				this.total_count_el.html('0');
			}

			setTimeout(function(){
				self.listings_parent.css('min-height', self.listings_parent.height());
			}, 400);
		},

		'sort': function (value) {
			switch (value) {
				case 'list-price-asc':
					this._sort('list-price', 'asc');
					break;
				case 'list-price-desc':
					this._sort('list-price', 'desc');
					break;
				case 'property-type-all':
					this.show_prices(0,999999999);
					break;
				case 'property-type-house':
					this.sort_property_type('house', 'house');
					break;
				case 'property-type-condo':
					this.sort_property_type('condo', 'condo');
					break;
				case 'property-type-townhome':
					this.sort_property_type('townhome', 'townhouse');
					break;
				case 'property-type-land':
					this.sort_property_type('land', 'land');
					break;
				case 'list-date-desc':
					this._sort('list-date', 'desc');
					break;
			}
			this.goto_page(1);
			this.current_sort = value;
		},

		'sort_onsamepage': function (value) {
			switch (value) {
				case 'list-price-asc':
					this._sort('list-price', 'asc');
					break;
				case 'list-price-desc':
					this._sort('list-price', 'desc');
					break;
				case 'list-date-desc':
					this._sort('list-date', 'desc');
					break;
			}
			this.current_sort = value;
			this._hovertrans();
		}
	});

	$(function () {
		$('#displet-tile').displet_tile();
	});
	
})(jQuery);

/*********************
Vertical Listings JS
*********************/

;(function ($) {
	$.widget('displetreader.displet_listings', {
		'options': {
			'first_selector': 'span.displet-listings-counts-first',
			'last_selector': 'span.displet-listings-counts-last',
			'total_selector': 'span.displet-listings-counts-total',
			'previous_selector': 'a.displet-listings-control-prev-link',
			'next_selector': 'a.displet-listings-control-next-link',
			'sortby_selector': '.displet-listings-sortby',
			'listing_selector': '.displet-listing',
			'selector_map': {
				'list-price': '.displet-listing-price-value',
				'list-date': '.displet-listing-list-date'
			}
		},

		'_create': function () {
			var el = this.element;
			var o = this.options;
			var self = this;

			this.active_listings = new Array();
			this.first_count_el = $(o.first_selector, el);
			this.last_count_el = $(o.last_selector, el);
			this.total_count_el = $(o.total_selector, el);
			this.prev_button = $(o.previous_selector, el);
			this.next_button = $(o.next_selector, el);
			this.listings = $(o.listing_selector, el);
			this.listings_parent = this.listings.parent();
			this.sortby_select = $(o.sortby_selector, el);

			this.first_index = Number($(this.first_count_el[0]).html()) - 1;
			this.last_index = Number($(this.last_count_el[0]).html()) - 1;
			this.total_count = Number($(this.total_count_el[0]).html());

			if (this.total_count > 0) {
				this.rpp = this.last_index + 1;

				if ($.cookie('displet_last_viewed_listings_page')) {
					this.current_page = parseInt($.cookie('displet_last_viewed_listings_page'));
				}
				else {
					this.current_page = 1;
				}

				$(this.sortby_select, el).bind('change', function (ev) {
					if (this.value) {
						self.sort(this.value);
						$.cookie('displet_last_viewed_listings_sort', this.value, {path: '/'});
					}
				});

				$(this.next_button).click(function (ev) {
					self.next_page();
					ev.preventDefault();
					return false;
				});

				$(this.prev_button).click(function (ev) {
					self.prev_page();
					ev.preventDefault();
					return false;
				});

				this._make_pages();
				
				if ($('#displet-listings').hasClass('price_low_to_high')) {
					this.current_sort = 'list-price-asc';
				}
				else if ($('#displet-listings').hasClass('price_high_to_low')) {
					this.current_sort = 'list-price-desc';
				}
				var sort_cookie_value = $.cookie('displet_last_viewed_listings_sort', {path: '/'});
				if (sort_cookie_value && sort_cookie_value != this.current_sort) {
					self.sort_onsamepage(sort_cookie_value);
				}

				this.listings.hide();

				this.goto_page(this.current_page);
			}
			else {
				this.first_count_el.html('0');
				this.last_count_el.html('0');
				this.total_count_el.html('0');
			}

			setTimeout(function(){
				self.listings_parent.css('min-height', self.listings_parent.height());
			}, 400);
		},

		'display_this_view': function () {
			this._make_pages();
			var last_viewed_sort = $('#displet-tile').data('displet_tile').last_viewed_sort();
			if (last_viewed_sort && last_viewed_sort != this.current_sort) {
				this.sort_onsamepage(last_viewed_sort);
			}
			var last_viewed_page = $('#displet-tile').data('displet_tile').last_viewed_page();
			if (last_viewed_page) {
				this.current_page = last_viewed_page;
			}
			else{
				this.current_page = 1;
			}
			$('#displet-tile').fadeOut(400);
			this.goto_page(this.current_page, true);
			$.cookie('displet_last_viewed_listings_orientation', 'vertical', {path: '/'});
		},

		'last_viewed_page': function () {
			return this.current_page;
		},

		'last_viewed_sort': function () {
			return this.current_sort;
		},

		'show_prices': function (minprice, maxprice) {
			this.listings.fadeOut(200);
			var i = 0;
			var total_price = 0;
			var total_square_feet = 0;
			var total_actives = 0;
			var lowest_price = 999999999;
			var highest_price = 0;
			var lowest_square_feet = 999999999;
			var highest_square_feet = 0;
			$('#displet-listings .displet-listing').each(function () {
				var listing_price = parseInt($(this).children('.displet-listing-info').children('.displet-listing-price').children('.displet-listing-price-value').text().replace(/(\W*)/g, ''));
				if (listing_price >= minprice && listing_price <= maxprice) {
					i++;
					$(this).addClass('displet-in-price-range');
					total_price += listing_price;
					if (listing_price > highest_price) {
						highest_price = listing_price;
					}
					if (listing_price < lowest_price) {
						lowest_price = listing_price;
					}
					var square_feet = parseInt($(this).children('.displet-listing-info').children('.displet-listing-specs').children('tbody').children('tr').children('td').children('.displet-listing-sq-ft').text().replace(/(\W*)/g, ''));
					if (square_feet > 0) {
						total_square_feet += square_feet;
						if (square_feet > highest_square_feet) {
							highest_square_feet = square_feet;
						}
						if (square_feet < lowest_square_feet) {
							lowest_square_feet = square_feet;
						}
					}
					if (!$(this).children('.displet-listing-thumb-container').children('div').hasClass('tileoverlay')) {
						total_actives++;
					}
				}
				else {
					$(this).hide();
					if ($(this).hasClass('displet-in-price-range')) {
						$(this).removeClass('displet-in-price-range');
					}	
				}
			});
			this.total_count = i;
			this.current_prices = total_price;
			this.current_square_footages = total_square_feet;
			this.current_actives = total_actives;
			this.current_highest_price = highest_price;
			this.current_lowest_price = lowest_price;
			this.current_highest_square_feet = highest_square_feet;
			this.current_lowest_square_feet = lowest_square_feet;
			$('#displet-listings span.displet-listings-counts-total').text(this.total_count);
			$('#displet-listings').displet_listings('option', {'listing_selector': '.displet-listing.displet-in-price-range'});

			var el = this.element;
			var o = this.options;
			var self = this;

			this.first_count_el = $(o.first_selector, el);
			this.last_count_el = $(o.last_selector, el);
			this.listings = $(o.listing_selector, el);

			this.first_index = Number($(this.first_count_el[0]).html()) - 1;
			this.last_index = Number($(this.last_count_el[0]).html()) - 1;

			if (this.total_count > 0) {
				this.current_page = 1;
				this._make_pages();
				this.goto_page(this.current_page);
			}
			else {
				this.first_count_el.html('0');
				this.last_count_el.html('0');
				this.total_count_el.html('0');
			}

			setTimeout(function(){
				self.listings_parent.css('height', self.listings_parent.height());
			}, 400);
		},
		
		'total_price': function () {
			return this.current_prices;
		},
		
		'highest_price': function () {
			return this.current_highest_price;
		},
		
		'lowest_price': function () {
			return this.current_lowest_price;
		},
		
		'total_square_feet': function () {
			return this.current_square_footages;
		},
		
		'highest_square_feet': function () {
			return this.current_highest_square_feet;
		},
		
		'lowest_square_feet': function () {
			return this.current_lowest_square_feet;
		},
		
		'total_listings': function () {
			return this.total_count;
		},
		
		'total_actives': function () {
			return this.current_actives;
		},

		'_hovertrans': function () {
			$.each(this.active_listings, function(){
				$(this).hover(
					function () {
						$(this).children('.displet-listing-hovertrans').stop(true, true).fadeIn(200);
						$(this).children('.displet-listing-info').children('.displet-text-overlay-hovertrans').fadeIn(200);
						$(this).children('.displet-listing-info').children('.displet-text-overlay').fadeOut(200);
					},
					function () {
						$(this).children('.displet-listing-hovertrans').stop(true, true).fadeOut(200);
						$(this).children('.displet-listing-info').children('.displet-text-overlay-hovertrans').fadeOut(200);
						$(this).children('.displet-listing-info').children('.displet-text-overlay').fadeIn(200);
					}
				);
			});
		},

		'_make_pages': function () {
			this.pages = {};

			var pages_count = Math.ceil(this.total_count / this.rpp);
			this.pages_count = pages_count;

			var page = 1;
			this.pages[page] = [];

			for (var i = 0; i < this.total_count; i++) {
				if (i < (page * this.rpp)) {
					this.pages[page].push(i);
				}
				else {
					page++;
					this.pages[page] = [];
					this.pages[page].push(i);
				}
			}
		},

		'_update_counts': function (first, last) {
			this.first_count_el.html(first);
			this.last_count_el.html(last);
		},

		'next_page': function () {
			this.goto_page(this.current_page + 1);
		},

		'prev_page': function () {
			this.goto_page(this.current_page - 1);
		},

		'goto_page': function(page, from_other_view) {
			if(typeof(from_other_view) === 'undefined') {
				from_other_view = false;
			}
			if (from_other_view && page > this.pages_count) {
				page = this.pages_count;
			}
			if ((typeof this.pages[page]) != 'undefined') {
				var self = this;

				if (this.active_listings.length && this.current_page != page) {
					$.each(this.active_listings, function(){
						$(this).fadeOut(400);
					});
				}
				else if (this.active_listings.length){
					$.each(this.active_listings, function(){
						$(this).hide();
					});
				}
				this.active_listings = new Array();

				var j = -1;
				$.each(this.pages[page], function (i,v) {
					var img = $(self.listings[v]).children('.displet-listing-thumb-container').children('img');
					$(img).attr('src', $(img).attr('value'));
					$(self.listings[v]).fadeIn(400);
					var hovertrans = $(self.listings[v]).children('.displet-listing-hovertrans');
					$(hovertrans).children('.displet-inner').children('.displet-inner2').css('width', $(hovertrans).parent('.displet-listing').width() + 14);
					$(hovertrans).children('.displet-inner').children('.displet-inner2').css('height', $(hovertrans).parent('.displet-listing').height() + 38);
					j++;
					self.active_listings.push($(self.listings[v]));
				});				

				this._update_counts(this.pages[page][0] + 1, this.pages[page][j] + 1);
				this.current_page = page;
				this._hovertrans();
				$.cookie('displet_last_viewed_listings_page', page);
			}
		},

		'_sort': function (field, dir) {
			this.listings = $('.displet-listing', this.element);

			var self = this;

			var filter_regex = /(\W*)/g;
			var digits_regex = /\D*/g
			var listings_array = this.listings.toArray();
			var map = this.options.selector_map;

			this.listings_parent.empty();

			listings_array.sort(function (a, b) {
				var a_val = $.trim($(map[field], a).html()).replace(filter_regex, '');
				var b_val = $.trim($(map[field], b).html()).replace(filter_regex, '');

				if (digits_regex.test(a_val)) {
					a_val = new Number(a_val);
					b_val = new Number(b_val);
				}

				if (dir === 'asc') {
					if (a_val > b_val) {
						return 1;
					}
					else if (a_val === b_val) {
						return 0;
					}
					else {
						return -1;
					}
				}
				else if (dir === 'desc') {
					if (a_val < b_val) {
						return 1;
					}
					else if (a_val === b_val) {
						return 0;
					}
					else {
						return -1;
					}
				}
			});

			$.each(listings_array, function () {
				self.listings_parent.append(this);
			});

			this.listings = $(this.options.listing_selector, this.element);
		},

		'sort': function (value) {
			switch (value) {
				case 'list-price-asc':
					this._sort('list-price', 'asc');
					break;
				case 'list-price-desc':
					this._sort('list-price', 'desc');
					break;
				case 'property-type-all':
					this.show_prices(0,999999999);
					break;
				case 'property-type-house':
					this.sort_property_type('house', 'house');
					break;
				case 'property-type-condo':
					this.sort_property_type('condo', 'condo');
					break;
				case 'property-type-townhome':
					this.sort_property_type('townhome', 'townhouse');
					break;
				case 'property-type-land':
					this.sort_property_type('land', 'land');
					break;
				case 'list-date-desc':
					this._sort('list-date', 'desc');
					break;
			}
			this.goto_page(1);
			this.current_sort = value;
		},

		'sort_property_type': function (property_type, property_type2) {
			this.listings.fadeOut(200);
			var i = 0;
			var total_price = 0;
			var total_square_feet = 0;
			var total_actives = 0;
			var lowest_price = 999999999;
			var highest_price = 0;
			var lowest_square_feet = 999999999;
			var highest_square_feet = 0;
			$('#displet-listings .displet-listing').each(function () {
				var listing_price = parseInt($(this).children('.displet-listing-info').children('.displet-listing-price').children('.displet-listing-price-value').text().replace(/(\W*)/g, ''));
				var property_type_value = $(this).find('.displet-listing-property-type').text().toLowerCase();
				if (property_type_value.indexOf(property_type) !== -1 || property_type_value.indexOf(property_type2) !== -1) {
					i++;
					$(this).addClass('displet-in-price-range');
					total_price += listing_price;
					if (listing_price > highest_price) {
						highest_price = listing_price;
					}
					if (listing_price < lowest_price) {
						lowest_price = listing_price;
					}
					var square_feet = parseInt($(this).children('.displet-listing-info').children('.displet-listing-specs').children('tbody').children('tr').children('td').children('.displet-listing-sq-ft').text().replace(/(\W*)/g, ''));
					if (square_feet > 0) {
						total_square_feet += square_feet;
						if (square_feet > highest_square_feet) {
							highest_square_feet = square_feet;
						}
						if (square_feet < lowest_square_feet) {
							lowest_square_feet = square_feet;
						}
					}
					if (!$(this).children('.displet-listing-thumb-container').children('div').hasClass('tileoverlay')) {
						total_actives++;
					}
				}
				else {
					$(this).hide();
					if ($(this).hasClass('displet-in-price-range')) {
						$(this).removeClass('displet-in-price-range');
					}	
				}
			});
			this.total_count = i;
			this.current_prices = total_price;
			this.current_square_footages = total_square_feet;
			this.current_actives = total_actives;
			this.current_highest_price = highest_price;
			this.current_lowest_price = lowest_price;
			this.current_highest_square_feet = highest_square_feet;
			this.current_lowest_square_feet = lowest_square_feet;
			$('#displet-listings span.displet-listings-counts-total').text(this.total_count);
			$('#displet-listings').displet_listings('option', {'listing_selector': '.displet-listing.displet-in-price-range'});

			var el = this.element;
			var o = this.options;
			var self = this;

			this.first_count_el = $(o.first_selector, el);
			this.last_count_el = $(o.last_selector, el);
			this.listings = $(o.listing_selector, el);

			this.first_index = Number($(this.first_count_el[0]).html()) - 1;
			this.last_index = Number($(this.last_count_el[0]).html()) - 1;

			if (this.total_count > 0) {
				this.current_page = 1;
				this._make_pages();
				this.goto_page(this.current_page);
			}
			else {
				this.first_count_el.html('0');
				this.last_count_el.html('0');
				this.total_count_el.html('0');
			}

			setTimeout(function(){
				self.listings_parent.css('height', self.listings_parent.height());
			}, 400);
		},

		'sort_onsamepage': function (value) {
			switch (value) {
				case 'list-price-asc':
					this._sort('list-price', 'asc');
					break;
				case 'list-price-desc':
					this._sort('list-price', 'desc');
					break;
				case 'list-date-desc':
					this._sort('list-date', 'desc');
					break;
			}
			this.current_sort = value;
			this._hovertrans();
		}
	});

	$(function () {
		$('#displet-listings').displet_listings();
	});
})(jQuery);

/*********************
Dynamic View JS
*********************/

jQuery(document).ready(function($){

	// Toggle view
	$('#displet-dynamic .displet-list-view').click(function() {
		$('#displet-tile').hide();
		$('#displet-listings').show();
		$('#displet-dynamic .displet-tile-view').removeClass('current');
		$('#displet-dynamic .displet-list-view').addClass('current');
		$('#displet-listings').displet_listings('display_this_view');		
	});
	$('#displet-dynamic .displet-tile-view').click(function() {
		$('#displet-listings').hide();
		$('#displet-tile').show();
		$('#displet-dynamic .displet-list-view').removeClass('current');
		$('#displet-dynamic .displet-tile-view').addClass('current');
		$('#displet-tile').displet_tile('display_this_view');
	});

});

/*********************
Price & Property Type Navigation JS
*********************/

function displetCommaSeparateNumber(val){
	while (/(\d+)(\d{3})/.test(val.toString())){
		val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
	}
	return val;
}

jQuery(document).ready(function($){

	// Filter listings, update stats
	$('#displet-price-navigation .displet-price-navigation').click(function() {
		if (!$(this).hasClass('active')) {
			displetUpdatePrices($(this).attr('displetminprice'), $(this).attr('displetmaxprice'), false, false, false, false, true);
		}
	});
	$('#displet-property-type-navigation .displet-property-type-navigation').click(function() {
		if (!$(this).hasClass('active')) {
			var propertytype = $(this).attr('displetpropertytype');
			var property_type_all = false;
			switch (propertytype) {
				case 'property-type-all':
					var propertytype1 = propertytype2 = false;
					property_type_all = true;
					break;
				case 'property-type-house':
					var propertytype1 = propertytype2 = 'house';
					break;
				case 'property-type-condo':
					var propertytype1 = propertytype2 = 'condo';
					break;
				case 'property-type-townhome':
					var propertytype1 = 'townhome';
					var propertytype2 = 'townhouse';
					break;
				case 'property-type-land':
					var propertytype1 = propertytype2 = 'land';
					break;
			}
			displetUpdatePrices($(this).attr('displetminprice'), $(this).attr('displetmaxprice'), true, propertytype1, propertytype2, property_type_all, true);
		}
	});
	$('#displet-tile .displet-tile-sortby').bind('change', function (ev) {
		if ($('option:selected', this).attr('displetminprice')) {
			var minprice = $('option:selected', this).attr('displetminprice');
			var maxprice = $('option:selected', this).attr('displetmaxprice');
			var property_type_adjustment = false;
			var propertytype1 = false;
			var propertytype2 = false;
			var value = $('option:selected', this).val();
			if (value.indexOf('property-type') !== -1) {
				property_type_adjustment = true;
				propertytype1 = propertytype2 = value.replace('property-type', '');
			}
			displetUpdatePrices(minprice, maxprice, property_type_adjustment, propertytype1, propertytype2, false, false);
		}
	});
	$('#displet-listings .displet-listings-sortby').bind('change', function (ev) {
		if ($('option:selected', this).attr('displetminprice')) {
			var minprice = $('option:selected', this).attr('displetminprice');
			var maxprice = $('option:selected', this).attr('displetmaxprice');
			var property_type_adjustment = false;
			var propertytype1 = false;
			var propertytype2 = false;
			var value = $('option:selected', this).val();
			if (value.indexOf('property-type') !== -1) {
				property_type_adjustment = true;
				propertytype1 = propertytype2 = value.replace('property-type', '');
			}
			displetUpdatePrices(minprice, maxprice, property_type_adjustment, propertytype1, propertytype2, false, false);
		}
	});
	function displetUpdatePrices(minprice, maxprice, property_type_adjustment, propertytype1, propertytype2, property_type_all, notfrombind) {
		var statsExist = $('#displet-stats').length;
		var tilesExist = $('#displet-tile').length;
		var verticalsExist = $('#displet-listings').length;
		var mapExists = $('#displet-map').length;

		if (statsExist) {
			$('#displet-stats').fadeOut(200);
		}
		if (!property_type_adjustment || property_type_all) {
			if (tilesExist) {
				$('#displet-tile').data('displet_tile').show_prices(minprice, maxprice);
			}
			if (verticalsExist) {
				$('#displet-listings').data('displet_listings').show_prices(minprice, maxprice);
			}
		}
		else if (propertytype1 && propertytype2 && notfrombind){
			if (tilesExist) {
				$('#displet-tile').data('displet_tile').sort_property_type(propertytype1, propertytype2);
			}
			if (verticalsExist) {
				$('#displet-listings').data('displet_listings').sort_property_type(propertytype1, propertytype2);
			}
		}

		if (tilesExist) {
			var orientation_element = '#displet-tile';
			var orientation_function = 'displet_tile';
		}
		else if (verticalsExist){
			var orientation_element = '#displet-listings';
			var orientation_function = 'displet_listings';
		}

		if (statsExist && (tilesExist || verticalsExist)) {
			var listings_total = $(orientation_element).data(orientation_function).total_listings();
			var actives_total = $(orientation_element).data(orientation_function).total_actives();
			var pending_total = listings_total - actives_total;
			var price_total = $(orientation_element).data(orientation_function).total_price();
			var price_highest = displetCommaSeparateNumber($(orientation_element).data(orientation_function).highest_price());
			var price_lowest = displetCommaSeparateNumber($(orientation_element).data(orientation_function).lowest_price());
			var price_average = displetCommaSeparateNumber(Math.round(price_total / listings_total));
			var square_feet_total = $(orientation_element).data(orientation_function).total_square_feet();
			var square_feet_highest = displetCommaSeparateNumber($(orientation_element).data(orientation_function).highest_square_feet());
			var square_feet_lowest = displetCommaSeparateNumber($(orientation_element).data(orientation_function).lowest_square_feet());
			var square_feet_average = displetCommaSeparateNumber(Math.round(square_feet_total / listings_total));
	
			$('#displet-stats .displet-active-count').text(actives_total);
			$('#displet-stats .displet-pending-count').text(pending_total);
			$('#displet-stats .displet-lowest-price').text(price_lowest);
			$('#displet-stats .displet-highest-price').text(price_highest);
			$('#displet-stats .displet-average-price').text(price_average);
			$('#displet-stats .displet-lowest-square-footage').text(square_feet_lowest);
			$('#displet-stats .displet-highest-square-footage').text(square_feet_highest);
			$('#displet-stats .displet-average-square-footage').text(square_feet_average);
			if (maxprice != 999999999) {
				$('#displet-stats .displet-lowest-price-title').text(displetCommaSeparateNumber(minprice));
				$('#displet-stats .displet-highest-price-title').text(displetCommaSeparateNumber(maxprice));
				$('#displet-stats .displet-prices').show();
			}
			else {
				$('#displet-stats .displet-prices').hide();
			}
		}

		if (mapExists) {
			$('#displet-map #map_canvas').empty();
			loadDispletMap(minprice,maxprice,true);
		}

		$('#displet-price-navigation .displet-price-navigation.active').removeClass('active');
		$('#displet-price-navigation .displet-price-navigation').each(function(){
			if ($(this).attr('displetminprice') == minprice) {
				$(this).addClass('active');
			}
		});

		if (property_type_adjustment) {
			$('#displet-property-type-navigation .displet-property-type-navigation.active').removeClass('active');
			if (propertytype1 && propertytype2) {
				$('#displet-property-type-navigation .displet-property-type-navigation').each(function(){
					var property_type = $(this).attr('displetpropertytype');
					if (property_type.indexOf(propertytype1||propertytype2) !== -1) {
						$(this).addClass('active');
					}
				});
			}
			else{
				$('#displet-property-type-navigation .displet-property-type-navigation').each(function(){
					var property_type = $(this).attr('displetpropertytype');
					if (property_type == 'property-type-all') {
						$(this).addClass('active');
					}
				});
			}
		}
		else if (!notfrombind){
			$('#displet-property-type-navigation .displet-property-type-navigation.active').removeClass('active');
			$('#displet-property-type-navigation .displet-property-type-navigation').each(function(){
				var property_type = $(this).attr('displetpropertytype');
				if (property_type == 'property-type-all') {
					$(this).addClass('active');
				}
			});
		}

		if (statsExist) {
			$('#displet-stats').fadeIn(200);
		}
	}

});

/*********************
Map JS
*********************/

jQuery(document).ready(function($){

	// Show/Hide map
	$('#displet-map .showhide .show').click(function() {
		$('#displet-map').removeClass('hiding');
		$('#displet-map').addClass('showing');
		$('#displet-map #map_canvas').hide();
		$('#displet-map #map_canvas').stop(true, true).slideDown(300);
	});
	$('#displet-map .showhide .hide').click(function() {
		$('#displet-map #map_canvas').stop(true, true).slideUp(300);
		setTimeout(function(){
			$('#displet-map').removeClass('showing');
			$('#displet-map').addClass('hiding');
		}, 300);
	});

});

/*********************
Sidescroller Widget JS
*********************/

jQuery(document).ready(function($){

	// Carousel
	try {
		$(".displet-listings-carousel").each(function(){
			$(this).displetCarouselLite({
			    btnNext: "a.displet-navigation-next",
			    btnPrev: "a.displet-navigation-previous",
			    visible: 1,
			    auto: 5000
			});
		});
  	}
  	catch(e){}

});

/*********************
Sidescroller JS
*********************/

;(function ($) {
	$.widget('displetreader.displetreader_sidescroller', {
		'options': {
			'scrollable_css': {
				'position': 'relative',
				'overflow': 'hidden'
			},

			'items_css': {
				'width': '20000em',
				'position': 'absolute',
			},

			'target_width': false
		},

		'_create': function () {
			var $el = $(this.element);
			this.listings = $('.displet-sidescroller-listing', $el);

			this.width = $el.width();

			this._get_greatest_height();

			this.scrollable = $('.scrollable', $el);
			this.scrollable.css(this.options.scrollable_css);

			this.scrollable_items = $('.items', this.scrollable);
			this.scrollable_items.css(this.options.items_css);

			this.listings.css({'float': 'left'});

			this.scrollable.width(this.width);
			this.listings.height(this.height);

			this.scrollable.height(this.scrollable_items.outerHeight('true'));

			this._divide_width();

			this.scrollable.scrollable();
		},

		'_get_greatest_height': function () {
			var self = this;
			this.height = 0;
			this.listings.each(function () {
				if ($(this).outerHeight(true) > self.height) {
					self.height = $(this).outerHeight(true);
				}
			});
		},

		'_divide_width': function () {
			if (this.options.target_width === false) {
				this.options.target_width = $(this.listings[0]).width();
			}

			var rpp = Math.floor(this.width / this.options.target_width);
			var result_width = this.width / rpp;

			var margin = Math.floor((Math.floor(result_width - this.options.target_width)) / 2);

			this.listings.css({'margin-left': margin + 'px', 'margin-right': margin + 'px'});
			this.rpp = rpp;
		}
	});

	$(function () {
		$(window).load(function () {
			$('.displet-sidescroller').displetreader_sidescroller();
		});
	});
})(jQuery);