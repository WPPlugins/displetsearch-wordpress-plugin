<?php
class DispletStats_Widget extends DispletReader_Widget
{
	protected $_default_title = 'Statistics';
	protected $_template = 'displet-stats-widget.php';
	protected $_stylesheet_filename = 'displet-styles.css';

	public function __construct() {
		WP_Widget::__construct(false, 'Displet Stats', array(
			'description' => 'Show statistics based on results from a Displet server.',
			'classname' => 'displet-stats-widget'
		));

		$this->_enqueue();
	}

	public function _enqueue() {
		DispletReader_View::enqueue_stylesheet($this->_stylesheet_filename);
	}

	public function widget($args, $instance) {
		$title = apply_filters('widget_title', $instance['title']);
		$this->_resultset = new DispletReader_ResultSet($instance['settings']['criteria']);
		$this->_resultset->init();

		echo $args['before_widget'];

		if ($title) {
			echo $args['before_title'];
			echo $title;
			echo $args['after_title'];
		}

		if ($this->_resultset->count() > 0) {
			$model = array(
				'subset' => $this->_resultset->fetch_subset(1, 3),
				'count' => $this->_resultset->count(),
				'price_range' => $this->_resultset->get_range('list-price'),
				'price_mean' => $this->_resultset->get_mean('list-price'),
				'price_median' => $this->_resultset->get_median('list-price'),
				'price_mode' => $this->_resultset->get_mode('list-price'),
				'size_range' => $this->_resultset->get_range('square-feet'),
				'results' => $this->_resultset->fetch_all()
			);
		}
		else {
			$model = array(
				'subset' => '',
				'count' => '',
				'price_range' => '',
				'price_mean' => '',
				'price_median' => '',
				'price_mode' => '',
				'size_range' => '',
				'results' => array()
			);
		}

		DispletReader_View::draw_template($this->_template, $model);

		echo $args['after_widget'];
	}
}
