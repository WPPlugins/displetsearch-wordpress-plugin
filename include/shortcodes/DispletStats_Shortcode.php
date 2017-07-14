<?php
class DispletStats_Shortcode extends DispletListing_Shortcode
{
	protected $_name = 'DispletStats';
	protected $_template_filename = 'displet-stats.php';
	protected $_stylesheet_filename = 'displet-styles.css';
	
	protected $_criteria;

	protected function _render() {
		if ($this->_resultset->count() > 0) {
			$model = array(
				'subset' => $this->_resultset->fetch_subset(1, 3),
				'count' => $this->_resultset->count(),
				'active' => $this->_resultset->count_active(),
				'price_range' => $this->_resultset->get_range('list-price'),
				'price_mean' => $this->_resultset->get_mean('list-price'),
				'price_median' => $this->_resultset->get_median('list-price'),
				'price_mode' => $this->_resultset->get_mode('list-price'),
				'size_range' => $this->_resultset->get_range('square-feet', false),
				'size_mean' => $this->_resultset->get_mean('square-feet'),
				'results' => $this->_resultset->fetch_all()
			);
		}
		else {
			$model = array(
				'subset' => '',
				'count' => '',
				'active' => '',
				'price_range' => '',
				'price_mean' => '',
				'price_median' => '',
				'price_mode' => '',
				'size_range' => '',
				'size_mean' => '',
				'results' => array()
			);
		}

		return DispletReader_View::get_template($this->_template_filename, $model);
	}

	protected function _enqueue() {
		DispletReader_View::enqueue_stylesheet($this->_stylesheet_filename);
	}
}
