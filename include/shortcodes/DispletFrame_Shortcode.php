<?php
class DispletFrame_Shortcode extends DispletListing_Shortcode
{
	protected $_name = 'DispletFrame';
	protected $_template_filename = 'displet-iframe.php';
	protected $_stylesheet_filename = 'displet-sidescroller.css';
	protected $_javascript_filename = 'displet-sidescroller.js';
	
	protected $_criteria;
	protected $_settings;
	
	protected function _init() {
		
		$this->_settings = $this->_model->get_settings();	
		
		$this->_criteria = $this->_filter_attributes($this->_attributes);
		
		require_once $this->_config->include_dir
				. DIRECTORY_SEPARATOR
				. 'php-displet'
				. DIRECTORY_SEPARATOR
				. 'Search.php';
			
		$search = new DispletReader_Displet_Search($this->_criteria, true);
		$this->_criteria  = $search->to_array(true);
		
	}
	
	public function callback($attributes = array()) {
		$this->_attributes = $attributes;
		$this->_init();
		return $this->_render();
	}
	
	protected function _render() {
		
		$criteria = '';
		
		foreach ($this->_criteria as $k => &$v) {
			$criteria .= "$k=$v&";
		}
		
		$iframe_url = $this->_settings['displet_url'];
		if (!empty($_GET['property'])) {
			$iframe_url .= 'residentials/detail/'. $_GET['property'];
		}
		else if (!empty($_GET))
		{
			if (!empty($_GET['map'])) {
				if ($_GET['map'] == 'true') {
					$iframe_url .= '/#';
					$search_separator = '/';
				} else {
					$iframe_url .= '/residentials/results/?';
					$search_separator = '&';
				}
				
				unset($_GET['map']);
			} else {
				$iframe_url .= '/residentials/results/?';
				$search_separator = '&';
			}
			
			foreach($_GET as $key=>$value) 
			{
				$iframe_url .= "$key=$value".$search_separator;
			}
		}
		else {
			// $iframe_url = $this->_settings['displet_url'] . 'residentials/results/';
			$iframe_url = $this->_settings['displet_url'];
			
			
			if (!empty($criteria)) {
				// $iframe_url .= "#" . rtrim($criteria,"&");
				$iframe_url .= "residentials/results/#" . rtrim($criteria,"&");
			}
		}
		
		$model = array(
				'width'=> $this->_attributes['width'],
		 		'height'=> $this->_attributes['height'],
				'iframe_url'=> $iframe_url );
				
		return DispletReader_View::get_template($this->_template_filename, $model);
	}
	
	protected function _filter_attributes($attributes = array()) {
		$criteria = array();
		if (!empty($attributes)) {
		foreach ($attributes as $k => $v) {
			
			if (!in_array($k, $this->_display_attributes)) {
				$criteria[$k] = $v;
			}
		}
		}
		return $criteria;
	}
}
