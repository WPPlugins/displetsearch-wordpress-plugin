<?php
class DispletListing_Widget extends DispletReader_Widget
{
	protected $_default_title = 'Featured Listings';
	protected $_template = 'displet-listings-widget.php';
	protected $_stylesheet_filename = 'displet-styles.css';

	public function __construct() {
		WP_Widget::__construct(false, 'Displet Listings Column', array(
			'description' => 'Show RETS/IDX listings in a column.',
			'classname' => 'displet-listings-widget'
		));

		$this->_enqueue();
	}

	protected function _enqueue() {
		DispletReader_View::enqueue_stylesheet($this->_stylesheet_filename);
	}
}
