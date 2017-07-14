<?php

function displet_q_search_scripts() {
	wp_register_script('modernizr', 'http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js', array('jquery'));
	wp_enqueue_script('modernizr');
}    
add_action('wp_enqueue_scripts', 'displet_q_search_scripts');

function displet_q_search_markup($dqs_color,$dqs_url,$dqs_orientation,$dqs_maxbeds,$dqs_maxbaths,$dqs_minprice,$dqs_maxprice,$dqs_priceincrement,$dqs_ondisplet,$dqs_title,$dqs_pricesorter,$dqs_includequickterms,$dqs_includepropertytype,$dqs_includeprice,$dqs_includebedsbaths,$dqs_includekeywords,$dqs_propertytypes) {
	
	$dqs_returnstring = '';
	if ($dqs_orientation=='vertical'){
		$orientation = 'vertical';
	}
	else {
		$orientation = 'horizontal';
	}
	$plugin_url = plugins_url() . '/displetsearch-wordpress-plugin/';
	
	// JavaScript
	$dqs_returnstring .= "
		<script>
		// Since using enqueue script to load jquery in no-conflict mode, must declare this to allow $ as shortcut for jQuery
		jQuery(document).ready(function($){

		  // Min Bedrooms
		  var minBedrooms = $('." . $orientation . " #dqs_minBedrooms');
		  for (var beds = 1;
		  beds <= " . $dqs_maxbeds . ";
		  beds++) {
		    $('<option value=" . '"' . "' + beds + '" . '"' . ">' + beds + '+</option>').appendTo(minBedrooms);
		  }

		  // Min Bathrooms
		  var minBathrooms = $('." . $orientation . " #dqs_minBathrooms');
		  for (var baths = 1;
		  baths <= " . $dqs_maxbaths . ";
		  baths++) {
		    $('<option value=" . '"' . "' + baths + '" . '"' . ">' + baths + '+</option>').appendTo(minBathrooms);
		  }

		  // Min Price
		  var minListPrice = $('." . $orientation . " #dqs_minListPrice');
		  for (var price = " . $dqs_minprice . ";
		  price <= " . $dqs_maxprice . ";
		  price += " . $dqs_priceincrement . ") {
		    $('<option value=" . '"' . "' + (price) + '" . '"' . ">' + displetCommaSeparateNumber(price) + ',000</option>').appendTo(minListPrice);
		  }

		  // Max Price
		  var maxListPrice = $('." . $orientation . " #dqs_maxListPrice');
		  for (var price = " . $dqs_minprice . ";
		  price <= " . $dqs_maxprice . ";
		  price += " . $dqs_priceincrement . ") {
		    $('<option value=" . '"' . "' + (price) + '" . '"' . ">' + displetCommaSeparateNumber(price) + ',000</option>').appendTo(maxListPrice);
		  }
		
		  // Property Type
		  var property_type = $('." . $orientation . " #dqs_property_type');
		  var property_type_array = [" . $dqs_propertytypes . "];
		  $.each(property_type_array, function () {
		    $('<option value=" . '"' . "' + this + '" . '"' . ">' + this + '</option>').appendTo(property_type);
		  });

		  // Clear form on reload
		  $('#dqs_quick_search input').val(''); 
		  $('#dqs_quick_search select').val('none');

		  if ('" . $dqs_ondisplet . "'==='yes') {
			var search_separator = '&';
			var search_starter = '?map=true&';
		  }
		  else {
			var search_separator = '/';
			var search_starter = '#';
		  }
		
		  function submitQSearch(url_base) {
			// Make sure the base URL contains 1 forward slash
			if (url_base.lastIndexOf('/') > url_base.length) {
		        url_base += '/';
			}
			url_base += search_starter;
			var url = url_base;
			
			// Add price sorter
			if ('" . $dqs_pricesorter . "' == 'price_low_to_high') {
				url += 'priceSorter=asc' + search_separator;
			}
			else {
				url += 'priceSorter=desc' + search_separator;
			}

			// Build the quick search parameters
		    $.each($('#dqs_quick_search." . $orientation . " .dqs_displet'), function () {
		      if (this.value != 'none' && this.value != '' && this.value != this.defaultValue && this.value != 'City, Zip Code, Neighborhood or Condo' && this.value != 'Ex. Pool, Modern, Foreclosure') {
		      url += this.name + '=' + this.value + search_separator;
			  }
		    });

		    window.location = url;
		  }
		
		  // Submit quick search on button click
		  $('#dqs_quick_search." . $orientation . " .action a').click(function(e) {
		  	e.preventDefault();
		    submitQSearch($(this).attr('href'));
			return false;
		  });
		
		  // Submit quick search on enter key
		  $('#dqs_quick_search." . $orientation . "').keypress(function(e)
		  {
			if (e.which == 13) {
				submitQSearch('" . $dqs_url . "');
				return false;
			}
		  });
		
		  // Submit quick search on spacebar key when button selected
		  $('#dqs_quick_search." . $orientation . " .action a').keypress(function(e)
		  {
			if (e.which == 32) {
				submitQSearch('" . $dqs_url . "');
				return false;
			}
		  });
		  
		  // Use modernizer to add placeholders for IE
		  if(!Modernizr.input.placeholder){
		  $('input').each(
		  function(){
		  if($(this).val()=='' && $(this).attr('placeholder')!=''){
		  $(this).val($(this).attr('placeholder'));
		  $(this).focus(function(){
		  if($(this).val()==$(this).attr('placeholder')) $(this).val('');
		  });
		  $(this).blur(function(){
		  if($(this).val()=='') $(this).val($(this).attr('placeholder'));
		  });
		  }
		  });
		  }

		// Ends allowance of jQuery to $ shortcut
		});</script>
	";
	
	// CSS
	$dqs_returnstring .= "
	<style>
	#dqs_quick_search *{
		outline: 0px;
		border: 0px;
		margin: 0px;
		padding: 0px;
		min-width: 0px;
		max-width: none;
		font-weight: normal;
	}
	#dqs_quick_search.horizontal{
		border: 1px solid #535353;
		padding: 4px;
		background: #787878 url('" . $plugin_url . "images/qsback.png') 0px 0px repeat-x;
		margin: 10px 0px;
	}
	#dqs_quick_search table, #dqs_quick_search *{
		padding:0;
		margin:0;
		border:0;
		background: none;
		text-align: left;
	}
	#dqs_quick_search table{
		width: 100%;
	}
	#dqs_quick_search.vertical .tit{
		line-height: 19px;
		padding: 12px 5px;
		text-align: center;
		font-size: 16px;
		font-weight: bold;
		font-family: 'Helvetica', 'Arial', sans-serif;
		color: #333;
		text-shadow: 0px 1px 2px #fff;
		border: 1px solid #afafaf;
		border-bottom: 0;
		background: #c6c6c6 url('" . $plugin_url . "images/qstitback.png') 0px 0px repeat-x;
	}
	#dqs_quick_search.vertical .inner{
		background: #fff;
		padding: 15px 10px 14px 10px;
		border: 1px solid #afafaf;
	}
	#dqs_quick_search.horizontal td{
		padding: 0px 5px;
		vertical-align: top;
	}
	#dqs_quick_search.horizontal td.fields{
		padding: 0px;
	}
	#dqs_quick_search.horizontal td.input{
		padding-right: 17px;
	}
	#dqs_quick_search.vertical td.input{
		width: 100%;
		padding: 0 12px 15px 0;
	}
	#dqs_quick_search.vertical td.input div{
		font-size: 10px;
		line-height: 7px;
		font-family: 'Arial', sans-serif;
		color: #333;
		font-weight: bold;
		text-transform: uppercase;
		margin-bottom: 10px;
	}
	#dqs_quick_search.vertical td.select{
		width: 50%;
		padding-bottom: 15px;
	}
	#dqs_quick_search.vertical .marr{
		margin-right: 6px;
	}
	#dqs_quick_search.vertical .marl{
		margin-left: 6px;
	}";
	$tdwidth = 15;
	$numbertds = 0;
	$numbertd2s = 0;
	if ($dqs_includeprice=='yes') $numbertds+=2;
	if ($dqs_includebedsbaths=='yes') $numbertds+=2;
	if ($dqs_includepropertytype=='yes') $numbertds++;
	if ($dqs_includekeywords=='yes') $numbertd2s++;
	if ($dqs_includequickterms=='yes') $numbertd2s++;
	if ($numbertds<4 && $numbertds>0 && $numbertd2s==2) $tdwidth = 40/$numbertds;
	if ($numbertds<4 && $numbertds>0 && $numbertd2s==1) $tdwidth = 60/$numbertds;
	if ($numbertds==0 && $numbertd2s==2) $tdwidth = 50;
	if ($numbertds==0 && $numbertd2s==1) $tdwidth = 100;
	if ($numbertds>=4 && $numbertd2s==2) $tdwidth = 60/$numbertds;
	if ($numbertds>=4 && $numbertd2s==1) $tdwidth = 80/$numbertds;
	if ($numbertds>0 && $numbertd2s==0) $tdwidth = 100/$numbertds;
	$dqs_returnstring .= "
	#dqs_quick_search.horizontal td.select{
		width: " . $tdwidth . "%;
	}
	#dqs_quick_search.horizontal td.action{
		width: 125px;
		min-width: 125px;
		max-width: 125px;
	}
	#dqs_quick_search input, #dqs_quick_search select{
		width:100%;
		font-family: 'Arial', sans-serif;
		font-size: 10px;
		font-weight: bold;
		text-transform: uppercase;
	}
	#dqs_quick_search.horizontal input, #dqs_quick_search.horizontal select{
		color: #333;
		border: 1px solid #676767;
		background: #fff url('" . $plugin_url . "images/qsinput.png') 0px 0px no-repeat;
	}
	#dqs_quick_search.vertical input, #dqs_quick_search.vertical select{
		color: #666;
		border: 1px solid #b6b6b6;
		background: #fff url('" . $plugin_url . "images/qsvinput.png') 0px 0px no-repeat;
	}
	#dqs_quick_search input{
		height: 28px;
		padding: 0px 5px;
		line-height: 28px;
	}
	#dqs_quick_search select{
		height: 30px;
		padding: 1px 1px 1px 5px;
		line-height: 26px;
		-moz-appearance: menulist;
		-webkit-appearance: menulist;
		appearance: menulist;
	}
	@-moz-document url-prefix() {
		#dqs_quick_search select {
			padding: 7px 1px 7px 5px;
		}
	}
	#dqs_quick_search.horizontal ::-webkit-input-placeholder {
	   color: #333;
	}
	#dqs_quick_search.horizontal :-moz-placeholder {  
	   color: #333;  
	}
	#dqs_quick_search.vertical ::-webkit-input-placeholder {
	   color: #666;
	}
	#dqs_quick_search.vertical :-moz-placeholder {  
	   color: #666;  
	}
	#dqs_quick_search #dqs_action{
		display: block;
		height: 28px;
		line-height: 28px;
		border: 1px solid #535353;
		text-align: center;
		font-size: 11px;
		color: #fff;
		font-family: 'Helvetica', 'Arial', sans-serif;
		font-weight: bold;
		text-transform: uppercase;
		text-decoration: none;
		background-repeat: repeat-x;
		background-position: 0px 0px;
	}
	#dqs_quick_search #dqs_action:hover{
		box-shadow: 0px 1px 3px #111;
	}
	#dqs_quick_search ." . $dqs_color . "{
		background-image: url('" . $plugin_url . "images/qs" . $dqs_color . ".png');
	}
	#dqs_quick_search #dqs_action.black{
		text-shadow: 0px -1px 2px #000;
		border-color: #000;
	}
	#dqs_quick_search .red{
		text-shadow: 0px -1px 2px #900;
	}
	#dqs_quick_search.vertical #dqs_action.red{
		border-color: #900;
	}
	#dqs_quick_search .green{
		text-shadow: 0px -1px 2px #090;
	}
	#dqs_quick_search.vertical #dqs_action.green{
		border-color: #44b149;
	}
	#dqs_quick_search .blue{
		text-shadow: 0px -1px 2px #009;
	}
	#dqs_quick_search.vertical #dqs_action.blue{
		border-color: #2572ac;
	}
	#dqs_quick_search .yellow{
		text-shadow: 0px -1px 2px #761;
	}
	#dqs_quick_search.vertical #dqs_action.yellow{
		border-color: #aba02d;
	}
	#dqs_quick_search .brown{
		text-shadow: 0px -1px 2px #291f18;
	}
	#dqs_quick_search.vertical #dqs_action.brown{
		border-color: #37281e;
	}
	</style>
	<!--[if lt IE 9]>
		<style>
		#dqs_quick_search select {
			padding: 7px 1px 7px 5px;
		}
		</style>
	<![endif]-->
	";
	
	// HTML Markup
	if ($dqs_orientation=='vertical') {
		$dqs_returnstring .= '
		<div id="dqs_quick_search" class="vertical">
			';  if ($dqs_title) $dqs_returnstring .= '<div class="tit">' . $dqs_title . '</div>'; $dqs_returnstring .= '
			<div class="inner">
				<table>'; if ($dqs_includequickterms=='yes') $dqs_returnstring .= '
					<tr>
						<td class="input" colspan="2">
							<input type="text" id="dqs_quickterms" name="quick_terms" class="dqs_displet" value="" placeholder="City, Zip Code, Neighborhood or Condo">
						</td>
					</tr>'; if ($dqs_includepropertytype=='yes') $dqs_returnstring .= '
					<tr>
						<td class="select" colspan="2" style="width:100%;">
							<select id="dqs_property_type" name="property_type" class="dqs_displet">
								<option value="none" selected="selected" disabled="disabled">Property Type</option>
							</select>
						</td>
					</tr>'; if ($dqs_includeprice=='yes') $dqs_returnstring .= '
					<tr>
						<td class="select">
							<div class="marr">
								<select id="dqs_minListPrice" name="minListPrice" class="dqs_displet">
									<option value="none" selected="selected" disabled="disabled">Min Price</option>
								</select>
							</div>
						</td>
						<td class="select">
							<div class="marl">
								<select id="dqs_maxListPrice" name="maxListPrice" class="dqs_displet">
									<option value="none" selected="selected" disabled="disabled">Max Price</option>
								</select>
							</div>
						</td>
					</tr>'; if ($dqs_includebedsbaths=='yes') $dqs_returnstring .= '
					<tr>
						<td class="select">
							<div class="marr">
								<select id="dqs_minBedrooms" name="minBedrooms" class="dqs_displet">
									<option value="none" selected="selected" disabled="disabled">Min Beds</option>
								</select>
							</div>
						</td>
						<td class="select">
							<div class="marl">
								<select id="dqs_minBathrooms" name="minBathrooms" class="dqs_displet">
									<option value="none" selected="selected" disabled="disabled">Min Baths</option>
								</select>
							</div>
						</td>
					</tr>'; if ($dqs_includekeywords=='yes') $dqs_returnstring .= '
					<tr>
						<td class="input" colspan="2">
							<div>Keywords</div>
							<input type="text" id="dqs_keyword" name="keyword" class="dqs_displet" value="" placeholder="Ex. Pool, Modern, Foreclosure">
						</td>
					</tr>'; $dqs_returnstring .= '
					<tr>
						<td class="action" colspan="2">
							<a href="' . $dqs_url . '" id="dqs_action" class="' . $dqs_color . '">View Results</a>
						</td>
					</tr>
				</table>
			</div>
		</div>
		';
	}
	else {
		$dqs_returnstring .= '
		<div id="dqs_quick_search" class="horizontal">
			<table>
				<tr>
					<td class="fields">
						<table>
							<tr>'; if ($dqs_includequickterms=='yes') $dqs_returnstring .= '
								<td class="input">
									<input type="text" id="dqs_quickterms" name="quick_terms" class="dqs_displet" value="" placeholder="City, Zip Code, Neighborhood or Condo">
								</td>'; if ($dqs_includepropertytype=='yes') $dqs_returnstring .= '
								<td class="select">
									<select id="dqs_property_type" name="property_type" class="dqs_displet">
										<option value="none" selected="selected" disabled="disabled">Property Type</option>
									</select>
								</td>'; if ($dqs_includeprice=='yes') $dqs_returnstring .= '
								<td class="select">
									<select id="dqs_minListPrice" name="minListPrice" class="dqs_displet">
										<option value="none" selected="selected" disabled="disabled">Min Price</option>
									</select>
								</td>
								<td class="select">
									<select id="dqs_maxListPrice" name="maxListPrice" class="dqs_displet">
										<option value="none" selected="selected" disabled="disabled">Max Price</option>
									</select>
								</td>'; if ($dqs_includebedsbaths=='yes') $dqs_returnstring .= '
								<td class="select">
									<select id="dqs_minBedrooms" name="minBedrooms" class="dqs_displet">
										<option value="none" selected="selected" disabled="disabled">Min Beds</option>
									</select>
								</td>
								<td class="select">
									<select id="dqs_minBathrooms" name="minBathrooms" class="dqs_displet">
										<option value="none" selected="selected" disabled="disabled">Min Baths</option>
									</select>
								</td>'; if ($dqs_includekeywords=='yes') $dqs_returnstring .= '
								<td class="input">
									<input type="text" id="dqs_keyword" name="keyword" class="dqs_displet" value="" placeholder="Ex. Pool, Modern, Foreclosure">
								</td>'; $dqs_returnstring .= '
							</tr>
						</table>
					</td>
					<td class="action">
						<a href="' . $dqs_url . '" id="dqs_action" class="' . $dqs_color . '">View Results</a>
					</td>
				</tr>
			</table>
		</div>
		';
	}
	
	return $dqs_returnstring;
	
}

function displet_q_search_shortcode( $atts ){
	$model = DispletReader_Woopa_Registry::get('model');
	$settings = $model->get_settings();
	$color = $settings['displet_quick_search_color'];
	extract(shortcode_atts(array(
		'color' => $color,
		'orientation' => 'horizontal',
		'title' => ''
	), $atts));
	$maxbeds = $settings['displet_quick_search_maxbeds'];
	$maxbaths = $settings['displet_quick_search_maxbaths'];
	$minprice = $settings['displet_quick_search_minprice'];
	$maxprice = $settings['displet_quick_search_maxprice'];
	$priceincrement = $settings['displet_quick_search_priceincrement'];
	$pricesorter = $settings['default_sort'];
	$includequickterms = $settings['include_quick_terms'];
	$includepropertytype = $settings['include_property_type'];
	$includeprice = $settings['include_price'];
	$includebedsbaths = $settings['include_beds_baths'];
	$includekeywords = $settings['include_keywords'];
	$propertytypes = $settings['displet_quick_search_propertytypes'];
	$displetpro = $settings['displet_pro_search'];
	if ($displetpro) {
		$ondisplet = 'no';
		$url = $settings['displet_url'];
	}
	else {
		$ondisplet = 'yes';
		$url = get_permalink($settings['ondisplet_landing_page']);
	}
	return displet_q_search_markup($color,$url,$orientation,$maxbeds,$maxbaths,$minprice,$maxprice,$priceincrement,$ondisplet,$title,$pricesorter,$includequickterms,$includepropertytype,$includeprice,$includebedsbaths,$includekeywords,$propertytypes);
}
add_shortcode('DispletQuickSearch', 'displet_q_search_shortcode');
add_filter('widget_text', 'do_shortcode');

class displet_q_search_widget extends WP_Widget {
	function displet_q_search_widget() {
		// Instantiate the parent object
		parent::__construct( false, 'Displet Quick Search' );
	}
	function widget( $args, $instance ) {
		// Widget output
		extract( $args );
		$model = DispletReader_Woopa_Registry::get('model');
		$settings = $model->get_settings();
		$color = $settings['displet_quick_search_color'];
		$maxbeds = $settings['displet_quick_search_maxbeds'];
		$maxbaths = $settings['displet_quick_search_maxbaths'];
		$minprice = $settings['displet_quick_search_minprice'];
		$maxprice = $settings['displet_quick_search_maxprice'];
		$priceincrement = $settings['displet_quick_search_priceincrement'];
		$pricesorter = $settings['default_sort'];
		$includequickterms = $settings['include_quick_terms'];
		$includepropertytype = $settings['include_property_type'];
		$includeprice = $settings['include_price'];
		$includebedsbaths = $settings['include_beds_baths'];
		$includekeywords = $settings['include_keywords'];
		$propertytypes = $settings['displet_quick_search_propertytypes'];
		$displetpro = $settings['displet_pro_search'];
		if ($displetpro) {
			$ondisplet = 'no';
			$url = $settings['displet_url'];
		}
		else {
			$ondisplet = 'yes';
			$url = get_permalink($settings['ondisplet_landing_page']);
		}
        $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$displet_q_search_widget_output = displet_q_search_markup($color,$url,'vertical',$maxbeds,$maxbaths,$minprice,$maxprice,$priceincrement,$ondisplet,$title,$pricesorter,$includequickterms,$includepropertytype,$includeprice,$includebedsbaths,$includekeywords,$propertytypes);
	    $displet_q_search_widget_output = $before_widget . $displet_q_search_widget_output . $after_widget;
		echo $displet_q_search_widget_output;
	}
	function update( $new_instance, $old_instance ) {
		// Save widget options
        return $new_instance;
	}
	function form( $instance ) {
		// Output admin widget options form
        $title = isset($instance['title']) ? strip_tags($instance['title']) : 'Search Properties';
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title: </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
		</p>
        <?php
	}
}
function displet_q_search_widget() {
	register_widget( 'displet_q_search_widget' );
}
add_action( 'widgets_init', 'displet_q_search_widget' );

?>
