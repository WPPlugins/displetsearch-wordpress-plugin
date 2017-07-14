<?php
class DispletSideScroller_Widget extends DispletReader_Widget
{
	protected $_default_title = 'Featured Listings';
	protected $_template = 'displet-sidescroller-widget.php';
	protected $_stylesheet = 'displet-styles.css';

	public function __construct() {
		WP_Widget::__construct(false, 'Displet Listing Scroller', array(
			'description' => 'Show RETS/IDX listings in a horizontal scroller.',
			'classname' => 'displet-sidescroller-widget'
		));

		$this->_enqueue();
	}

	protected function _enqueue() {
		wp_register_script('displet-carousel',
			DispletReader_Woopa_Registry::get('config')->url . '/'
				. 'js' . '/'
				. 'displet-carousel.js' ,
			array('jquery'));
		wp_enqueue_script('displet-carousel', array('jquery'));
		DispletReader_View::enqueue_javascript('displet-scripts.js', false, array('jquery'));

		DispletReader_View::enqueue_stylesheet($this->_stylesheet);
	}
}
