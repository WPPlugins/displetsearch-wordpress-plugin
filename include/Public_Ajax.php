<?php
class DispletReader_Public_Ajax
{
	public static function sort() {
		check_ajax_referer('displetreader_sort', 'displetreader_sort_nonce');
		self::_sort($_GET['sort_by'], 'ASC', $_GET['criteria']);
		die();
	}

	protected static function _sort($sortby, $direction, array $criteria) {
		$resultset = new DispletReader_ResultSet($criteria);
		$resultset->init();
		$resultset->sort($sortby, $direction);

		header('Content-type: application/json');

		// to make sure CDATA causes no probs
		echo json_encode(new SimpleXMLElement($resultset->fetch_all()->asXML(),
			LIBXML_NOCDATA));
	}
}
