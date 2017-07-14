<?php
abstract class DispletReader_Widget extends WP_Widget
{
	public function form($instance) {
		$title = (empty($instance['title'])) ? $this->_default_title : $instance['title'];
		$title = esc_attr($title);
		$settings = (empty($instance['settings'])) ? array() : $instance['settings'];
?>
<p>
<label for="<?php echo $this->get_field_id('title') ?>"><?php _e('Title') ?></label>
<input type="text"
	name="<?php echo $this->get_field_name('title') ?>"
	id="<?php echo $this->get_field_id('title') ?>" value="<?php echo $title ?>"/>
</p>
<p>
<div class="displet-widget-control">
	<input type="hidden" id="<?php echo $this->get_field_id('settings') ?>"
		name="<?php echo $this->get_field_name('settings') ?>"
		class="displet-widget-control-settings"
		value="<?php echo esc_attr(self::_serialize_for_jquery($settings)) ?>"/>
	<button class="displet-widget-configure">Configure</button>

	<span class="displet-widget-control-markup" style="display: none">
<?php
		// to avoid nesting forms, we must encode the markup for the dialog
		$array = array();
		echo htmlspecialchars(DispletReader_View::get_admin_template('widget_form_dialog.phtml',
			$array));
?>
	</span>
</div>
</p>
<?php		
	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$array = array();
		parse_str($new_instance['settings'], $array);
		$instance['settings'] = $array;

		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	public function widget($args, $instance) {
		$title = apply_filters('widget_title', $instance['title']);

		if (isset($instance['settings']['criteria'])
			&& is_array($instance['settings']['criteria'])) {
			$criteria = $instance['settings']['criteria'];
		}
		else {
			$criteria = false;
		}

		echo $args['before_widget'];

		if ($title) {
			echo $args['before_title'];
			echo $title;
			echo $args['after_title'];
		}

		if ($criteria) {
			$resultset = new DispletReader_ResultSet($instance['settings']['criteria']);
			$resultset->init();

			if(empty($instance['settings']['sortby'])){
				$instance['settings']['sortby'] = DispletReader_Woopa_Registry::get('model')->get_default_sort();
			}
			self::_parse_sort($instance['settings']['sortby'], $resultset);

			$model = array();
			$model['results'] = $resultset->fetch_all();
			$model['nothumb_url'] = DispletReader_Woopa_Registry::get('config')->nothumb_url;
			$model['use_pro'] = DispletReader_Woopa_Registry::get('model')
				->get_pro_search_enabled();
			$model['max_listings'] = $instance['settings']['max_listings'];

			if (!$model['use_pro']) {
				$model['landing_page'] = DispletReader_Woopa_Registry::get('model')->get_ondisplet_landing_page();
			}

			echo DispletReader_view::get_template($this->_template, $model);
		}
		else {
			echo '<i>Please configure your DispletReader widget.</i>';
		}
		echo $args['after_widget'];
	}

	// format widget settings so jquery can pick them up
	protected static function _serialize_for_jquery(array $settings) {
		$serialized = http_build_query($settings);
		return $serialized;
	}

	protected static function _parse_sort($sort, &$resultset) {
		switch ($sort) {
		case 'price_low_to_high':
			$resultset->sort('list-price', 'ASC');
			break;
		case 'price_high_to_low':
			$resultset->sort('list-price', 'DESC');
			break;
		case 'list_date_desc':
			$resultset->sort('list-date', 'DESC');
			break;
		}
	}
}
