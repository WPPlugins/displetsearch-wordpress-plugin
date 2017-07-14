<?php
class DispletReader_Admin_Ajax extends DispletReader_Woopa_Admin_Ajax
{
	public function process_post(array $post) {
		
	}

	public function process_get(array $get) {
		switch ($get['subaction']) {
			case 'get_listing_defaults':
				$this->_get_listing_defaults($get);
				break;
		}
	}

	protected function _get_listing_defaults($get) {
		$default_sort = $this->model->get_default_sort();
		$default_orientation = $this->model->get_default_orientation();

		$array = array(
			'default_sort' => $default_sort,
			'default_orientation' => $default_orientation
		);

		header('Content-type: application/json');
		echo json_encode($array);
	}
}
