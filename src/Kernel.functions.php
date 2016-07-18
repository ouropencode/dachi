<?php
/**
 * This file contains all of the global functions that help Dachi function.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    Mixed (many public online sources)
 */

/**
 * This function reverses the PHP function 'parse_url' and also allows for
 * ['query'] to be an array.
 *
 * @param array   $parsed_url
 * @return string
 */
function unparse_url($parsed_url) {
	$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
	$host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
	$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
	$user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
	$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
	$pass     = ($user || $pass) ? "$pass@" : '';
	$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
	$query    = "";
	if (isset($parsed_url['query'])) {
		if (is_array($parsed_url['query'])) {
			$query = "?";
			foreach ($parsed_url['query'] as $key => $val)
				$query .= $key . "=" . $val . "&";
			$query = substr($query, 0, -1);
		} else {
			$query = "?" . $parsed_url['query'];
		}
	}
	$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
	return "$scheme$user$pass$host$port$path$query$fragment";
}

if (!function_exists("array_column")) {
	/**
	 * Emulates the internal PHP function array_column. For pre-5.5 PHP.
	 *
	 * @param array $array
	 * @param string $column
	 * @return string
	 */
	function array_column($array, $column) {
		$output = array();
		foreach ($array as $key => $value)
			$output[$key] = $value[$column];
		return $output;
	}
}

/**
 * Retrieve how long (in milliseconds) it's been since the first call of this
 * function.
 *
 * @return float
 */
function getExecutionTime() {
	static $microtime_start = null;
	if ($microtime_start === null) {
		$microtime_start = microtime(true);
		return 0.0;
	}
	return microtime(true) - $microtime_start;
}

/**
 * Do a CURL request with URL, POST data and CURL options
 *
 * @param string  $URL
 * @param array   $postData         (optional)
 * @param array   $curlOptionsInput (optional)
 * @return string
 */
function curl_get_contents($URL, $postData = array(), $curlOptionsInput = array()) {
	// Default cURL settings
	$curlOptions = array (
		CURLOPT_URL            => $URL,
		CURLOPT_VERBOSE        => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST           => count($postData) ? true : false,
		CURLOPT_POSTFIELDS     => $postData,
		CURLOPT_CUSTOMREQUEST  => count($postData) ? 'POST' : 'GET',
	);

	foreach ($curlOptionsInput as $key => $val)
		$curlOptions[$key] = $val;

	// Initiate cURL connection
	$ch = curl_init();
	curl_setopt_array($ch, $curlOptions);

	// Sending our request - $response will hold the API response
	$response = curl_exec($ch);
	curl_close($ch);

	return $response;
}

/**
 * Performs json_encode, echos the result and returns true.
 *
 * @param object $object
 * @return bool
 */
function json_echo($object) {
	header("Content-type: application/json");
	echo json_encode($object);
	return true;
}

/**
 * Returns the calling namespace of the parent
 * @return string
 */
function get_calling_namespace() {
	$trace = debug_backtrace();
	if(!isset($trace[2]) || !isset($trace[2]['class']))
		return "";
	
	$reflect = new ReflectionClass($trace[2]['class']);
	return $reflect->getNamespaceName();
}

/**
 * Convert PHP error code number into human readable string.
 *
 * @param error   $type
 * @return string
 */
function human_error_code($type) {
	$return ="";
	if ($type & E_ERROR) // 1 //
		$return.='& E_ERROR ';
	if ($type & E_WARNING) // 2 //
		$return.='& E_WARNING ';
	if ($type & E_PARSE) // 4 //
		$return.='& E_PARSE ';
	if ($type & E_NOTICE) // 8 //
		$return.='& E_NOTICE ';
	if ($type & E_CORE_ERROR) // 16 //
		$return.='& E_CORE_ERROR ';
	if ($type & E_CORE_WARNING) // 32 //
		$return.='& E_CORE_WARNING ';
	if ($type & E_CORE_ERROR) // 64 //
		$return.='& E_COMPILE_ERROR ';
	if ($type & E_CORE_WARNING) // 128 //
		$return.='& E_COMPILE_WARNING ';
	if ($type & E_USER_ERROR) // 256 //
		$return.='& E_USER_ERROR ';
	if ($type & E_USER_WARNING) // 512 //
		$return.='& E_USER_WARNING ';
	if ($type & E_USER_NOTICE) // 1024 //
		$return.='& E_USER_NOTICE ';
	if ($type & E_STRICT) // 2048 //
		$return.='& E_STRICT ';
	if ($type & E_RECOVERABLE_ERROR) // 4096 //
		$return.='& E_RECOVERABLE_ERROR ';
	if ($type & E_DEPRECATED) // 8192 //
		$return.='& E_DEPRECATED ';
	if ($type & E_USER_DEPRECATED) // 16384 //
		$return.='& E_USER_DEPRECATED ';
	return substr($return, 2, -1);
}

function base64url_encode($i) {
	return str_replace(array('+', '/'), array('-', '_'), base64_encode($i));
}

function base64url_decode($i) {
	return base64_decode(str_replace(array('-', '_'), array('+', '/'), $i));
}