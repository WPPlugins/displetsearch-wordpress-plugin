<?php
/**
 * @defgroup php_displet
 * @version 1.1
 * Library for communication with DispletReader_Displet servers.
 *
 * Typical use: Instantiate a DispletReader_Displet_Search object with an array of DispletReader_Displet search criteria.
 * Create a DispletReader_Displet_Request object, passing the DispletReader_Displet_Search instance and an array of options
 * to its constructor. Call the Requests's send() method to get a DispletReader_Displet_Response object
 * containing the HTTP response code, raw data, and a SimpleXML representation of the results.
 *
 * Example:<br>
 * $search = new DispletReader_Displet_Search(array('maxListPrice' => '500'));<br>
 * $request = new DispletReader_Displet_Request($search, array('url' => 'http://foo.displet.com/'));<br>
 * $response = $request->send();<br>
 * $xml = $response->get_xml();
 */
$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
require_once $dir . 'Request.php';
require_once $dir . 'Response.php';
require_once $dir . 'Search.php';
