<?php
class DispletListing_Shortcode extends DispletReader_Shortcode
{
	protected $_name = 'DispletListing';
	protected $_template_filename = 'displet-listings.php';
	protected $_stylesheet_filename = 'displet-styles.css';
	protected $_javascript_filename = 'displet-scripts.js';
	
	protected $_display_attributes = array(
		'caption',
		'results_per_page',
		'sort_by',
		'order',
		'orientation',
		'width',
		'height',
		'map',
		'map_width',
		'map_height',
		'listings',
		'stats'
	);

	protected $_sorts = array(
		'price_high_to_low' => array(
			'field' => 'list-price',
			'order' => 'DESC'
		),
		'price_low_to_high' => array(
			'field' => 'list-price',
			'order' => 'ASC'
		),
		'list_date_desc' => array(
			'field' => 'list-date',
			'order' => 'DESC'
		)
	);

	protected $_criteria;
	protected $_resultset;
	protected $_settings;
	
	protected function _init() {
		
		$this->_settings = $this->_model->get_settings();	
		
		$this->_criteria = $this->_filter_attributes($this->_attributes);
		
		$this->_resultset = new DispletReader_ResultSet($this->_criteria);

		$this->_enqueue();
		
		$this->_resultset->init();
			
	}

	protected function _enqueue() {
		// load all javascript and css for now.
		
		DispletReader_View::enqueue_stylesheet('displet-styles.css');
		DispletReader_View::enqueue_javascript('displet-scripts.js', false, array('jquery','jquery-ui-widget'));
		DispletReader_View::enqueue_javascript('jquery.cookie.js', false, array('jquery'));
		
	}
	
	protected function _render() {
		
		$default_orientation = $this->_model->get_default_orientation();

		if (isset($this->_attributes['orientation'])) {
			$orientation = $this->_attributes['orientation'];
		}
		else if (!empty($default_orientation)) {
			$orientation = $default_orientation;
		}
		
		switch ($orientation) {
		case 'vertical':
			$this->_template_filename = 'displet-dynamic.php';
			$this->_default_view = 'vertical';
			break;
		case 'tile':
			$this->_template_filename = 'displet-dynamic.php';
			$this->_default_view = 'tile';
			break;
		case 'custom':
			$this->_template_filename = 'custom';
			$this->_default_view = 'custom';
			break;	
		}

		$settings = $this->_model->get_settings();

		if (isset($this->_attributes['sort_by'])) {
			$order = null;

			if (isset($this->_attributes['order'])) {
				$order = strtoupper($this->_attributes['order']);
			}

			$this->_resultset->sort($this->_attributes['sort_by'],
				$order);
		}
		else {
			$sort_slug = $this->_model->get_default_sort();

			$this->_resultset->sort($this->_sorts[$sort_slug]['field'],
				$this->_sorts[$sort_slug]['order']);
		}

		$model = array(
			'caption' => $this->_attributes['caption'],
			'use_pro' => $this->_settings['displet_pro_search'], 
			'criteria' => $this->_criteria,
			'results' => $this->_resultset->fetch_all(),
			'count' => $this->_resultset->count(),
			'nothumb_url' => $this->_config->nothumb_url,
			'map' => $this->_attributes['map'],
			'map_hide' => $this->_settings['map_hide'],
			'map_width' => $this->_settings['map_width'],
			'map_height' => $this->_settings['map_height'],
			'map_latlong_distance' => $this->_settings['map_latlong_distance'],
			'include_listing_office' => $this->_settings['include_listing_office'],
			'listings' => $this->_attributes['listings'],
			'stats' => $this->_attributes['stats'],
			'show_pending_count' => $this->_settings['show_pending_count'],
			'remove_link' => $this->_settings['remove_link'],
			'price_navigation' => $this->_settings['price_navigation'],
			'property_type_navigation' => $this->_settings['property_type_navigation'],
			'default_view' => $this->_default_view,
			'sort_by' => $sort_slug,
		);
		
		if ($settings['map'] == 'yes' && $model['map'] != 'no') {
			$model['map'] = 'yes';
		}
		
		if (isset($this->_attributes['map_width'])) {
			$model['map_width'] = $this->_attributes['map_width'];
		}

		if (isset($this->_attributes['map_height'])) {
			$model['map_height'] = $this->_attributes['map_height'];
		}
		elseif ($this->_settings['map_height'] != ''){
			$model['map_height'] = $this->_settings['map_height'];
		}
		else {
			$model['map_height'] = '180';
		}
		
		if ($settings['stats'] == 'yes' && $model['stats'] != 'no') {
			$model['stats'] = 'yes';
		}

		if ($_COOKIE['displet_last_viewed_listings_orientation']) {
			$model['default_view'] = $_COOKIE['displet_last_viewed_listings_orientation'];
		}
		
		if (!$this->_settings['displet_pro_search']) {
			$model['landing_page'] = $this->_settings['ondisplet_landing_page'];
		}
		
		if ($settings['disclaimers_enabled'] == 'true') {
			$model['disclaimer'] = $settings['disclaimer'];
		}

		if (isset($this->_attributes['results_per_page'])) {
			$model['rpp'] = (int) $this->_attributes['results_per_page'];
		}
		else {
			$model['rpp'] = $this->_model->get_rpp();
		}
		
		$returnstring = '';
		if ($model['property_type_navigation'] == 'yes' && $model['listings'] != 'no' && $model['count'] > 9) {
			$has_property_types = $this->_resultset->get_property_types();
			if ($has_property_types['more_than_one']) {
				$model2 = array(
					'has_houses' => $has_property_types['houses'],
					'has_condos' => $has_property_types['condos'],
					'has_townhomes' => $has_property_types['townhomes'],
					'has_land' => $has_property_types['land'],
				);
				$model = array_merge($model,$model2);
				$returnstring .= DispletReader_View::get_template('displet-property-type-navigation.php', $model);
			}
		}
		if ($model['price_navigation'] != 'no' && $model['listings'] != 'no' && $model['count'] > 9) {
			$model2 = array(
				'nav_elements' => $this ->_resultset->get_nav_elements()
			);
			if (isset($this->_attributes['sort_by'])) {
				$order = null;
		
				if (isset($this->_attributes['order'])) {
					$order = strtoupper($this->_attributes['order']);
				}
		
				$this->_resultset->sort($this->_attributes['sort_by'],
					$order);
			}
			else {
				$sort_slug = $this->_model->get_default_sort();
		
				$this->_resultset->sort($this->_sorts[$sort_slug]['field'],
					$this->_sorts[$sort_slug]['order']);
			}
			$model = array_merge($model,$model2);
			$returnstring .= DispletReader_View::get_template('displet-price-navigation.php', $model);
		}
		if ($model['stats'] == 'yes') {
			if ($this->_resultset->count() > 0) {
				$model2a = array(
					'subset' => $this->_resultset->fetch_subset(1, 3),
					'active' => $this->_resultset->count_active(),
					'price_range' => $this->_resultset->get_range('list-price'),
					'price_mean' => $this->_resultset->get_mean('list-price'),
				);
				if ($this->_resultset->count_land_and_ranch() == 0) {
					$model2b = array(
						'size_range' => $this->_resultset->get_range('square-feet', false),
						'size_mean' => $this->_resultset->get_mean('square-feet')
					);
					$model2 = $model2a + $model2b;
				}
				else {
					$model2 = $model2a;
				}
			}
			else {
				$model2 = array(
					'subset' => '',
					'active' => '',
					'price_range' => '',
					'price_mean' => '',
					'size_range' => '',
					'size_mean' => ''
				);
			}
			$model = array_merge($model,$model2);
			$returnstring .= DispletReader_View::get_template('displet-stats.php', $model);
		}
		if ($model['map'] == 'yes' && $model['count'] > 0) {
			$returnstring .= DispletReader_View::get_template('displet-map.php', $model);
		}
		if ($model['listings'] != 'no') {
			$returnstring .= DispletReader_View::get_template($this->_template_filename, $model);
		}

		return $returnstring;
	}

	protected function _filter_attributes(array $attributes) {
		$criteria = array();
		foreach ($attributes as $k => $v) {
			
			if (!in_array($k, $this->_display_attributes)) {
				$criteria[$k] = $v;
			}
		}
		return $criteria;
	}
}
