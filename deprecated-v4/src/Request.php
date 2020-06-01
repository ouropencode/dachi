<?php
namespace Dachi\Core;

/**
 * The Request class is responsable for storing the request data.
 *
 * This contains all data that modules and helpers require to work out
 * exactly what has been requested and carries with it the page arguments,
 * session arguments and any other data the request requires.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    $ourOpenCode
 */
class Request {
	protected static $uri           = array();
	protected static $uri_variables = array();
	protected static $arguments     = array();
	protected static $api_mode      = false;

	protected static $render_path   = "";

	protected static $output_data   = array();
	protected static $response_code = array("status" => "assumed", "message" => "Assuming successful.");

	/**
	 * Get a section of the URI
	 *
	 * The URI is split by the '/' character.
	 *
	 * @param  string $index The index to retrieve
	 * @param  string $regex The regular expression pattern the index must conform too
	 * @throws InvalidRequestURIException The specified index was not found or did not conform to the regex.
	 * @return string
	 */
	public static function getUri($index, $regex = ".*") {
		if(self::$uri === array())
			self::$uri = explode("/", trim($_GET['dachi_uri'], " \t\n\r\0\x0B/"));

		if(isset(self::$uri_variables[$index]))
			if($regex == ".*" || preg_match("/^" . $regex . "$/i", self::$uri_variables[$index]))
				return self::$uri_variables[$index];


		if(isset(self::$uri[$index]))
			if($regex == ".*" || preg_match("/^" . $regex . "$/i", self::$uri[$index]))
				return self::$uri[$index];

		throw new InvalidRequestURIException;
	}

	/**
	 * Get the whole URI
	 *
	 * The URI is split by the '/' character.
	 *
	 * @return array
	 */
	public static function getFullUri() {
		if(!isset($_GET['dachi_uri']))
			$_GET['dachi_uri'] = "";

		if(self::$uri === array() && isset($_GET['dachi_uri']))
			self::$uri = explode("/", trim($_GET['dachi_uri'], " \t\n\r\0\x0B/"));

		return self::$uri;
	}

	/**
	 * Set the variable mapping for URI componants.
	 *
	 * This is an internal function that allows the Router to assign the URI variables.
	 *
	 * @internal
	 * @see Router
	 * @param array $uri_variables Format: array(array(uri index, variable name))
	 * @param bool $api_mode Is this request serving an API method.
	 */
	public static function setRequestVariables($uri_variables, $api_mode = false) {
		if(self::$uri === array() && isset($_GET['dachi_uri']))
			self::$uri = explode("/", trim($_GET['dachi_uri'], " \t\n\r\0\x0B/"));

		foreach($uri_variables as $var)
			self::$uri_variables[$var[1]] = self::$uri[$var[0]];

		self::$api_mode = $api_mode;
	}

	/**
	 * Set the alternative render path for none AJAX requests
	 *
	 * This is an internal function that allows the Router to provide information on the render path.
	 *
	 * @internal
	 * @see Router
	 * @param string $renderPath The alternative url path for this request
	 */
	public static function setRenderPath($renderPath) {
		self::$render_path = $renderPath;
	}

	/**
	 * Get the alternative render path for none AJAX requests
	 *
	 * This is an internal function that allows the Router to provide information on the render path.
	 *
	 * @internal
	 * @see Router
	 * @return string
	 */
	public static function getRenderPath() {
		return self::$render_path;
	}

	/**
	 * Get an argument passed to the page
	 *
	 * This is used for _GET, _POST and _FILES.
	 *
	 * @param  string $key     The key to retrieve
	 * @param  string $default The default value to return if the key was not found
	 * @param  string $regex   The regular expression pattern the key must conform too
	 * @throws InvalidRequestArgumentException The specified key did not conform to the regex.
	 * @return string
	 */
	public static function getArgument($key, $default = "default", $regex = ".*") {
		if(self::$arguments === array())
			self::$arguments = array_merge($_GET, $_POST, $_FILES);

		if(isset(self::$arguments[$key])) {
			if($regex == ".*" || preg_match("/^" . $regex . "$/i", self::$arguments[$key]))
				return self::$arguments[$key];

			throw new InvalidRequestArgumentException;
		}

		return $default;
	}

	/**
	 * Get all arguments passed to the page
	 *
	 * This is used for _GET, _POST and _FILES.
	 *
	 * @return string
	 */
	public static function getAllArguments() {
		if(self::$arguments === array())
			self::$arguments = array_merge($_GET, $_POST, $_FILES);

		return self::$arguments;
	}

	/**
	 * Get a value from the user's session
	 *
	 * @param  string $key     The key to retrieve
	 * @param  string $default The default value to return if the key was not found
	 * @return string
	 */
	public static function getSession($key, $default = "default") {
		if(isset($_SESSION[$key]))
			return $_SESSION[$key];

		return $default;
	}

	/**
	 * Set a value in the user's session
	 *
	 * @param  string $key   The key to set
	 * @param  string $value The value to set
	 * @return string
	 */
	public static function setSession($key, $value) {
		return ($_SESSION[$key] = $value);
	}

	/**
	 * Get a value from the user's cookie
	 *
	 * @param  string $key     The key to retrieve
	 * @param  string $default The default value to return if the key was not found
	 * @return string
	 */
	public static function getCookie($key, $default = "default") {
		if(isset($_COOKIE[$key]))
			return $_COOKIE[$key];

		return $default;
	}

	/**
	 * Set a value in the user's cookie
	 * @param  string  $key    The key to set
	 * @param  string  $value  The value to set
	 * @param  integer $expire The time to expire (defaults to -1, -1 is 'now + 30 days')
	 * @param  string  $path   The cookie path inside the domain (defaults to '/')
	 * @param  string  $domain The domain this cookie is assigned to (defaults to server default)
	 * @return bool
	 */
	public static function setCookie($key, $value, $expire = -1, $path = "/", $domain = null) {
		if($expire == -1)
			$expire = time() + 2592000; // 30 days

		return setcookie($key, $value, $expire, $path, $domain);
	}

	/**
	 * Get an outgoing request data variable
	 * @param  string  $key    The key to get
	 * @return mixed
	 */
	public static function getData($key) {
		if(!isset(self::$output_data[$key]))
			return false;

		return self::$output_data[$key];
	}

	/**
	 * Get all outgoing request data
	 * @return array
	 */
	public static function getAllData() {
		return self::$output_data;
	}

	/**
	 * Check if an outgoing request data variable exists
	 * @param  string  $key    The key to check
	 * @return bool
	 */
	public static function hasData($key) {
		return isset(self::$output_data[$key]);
	}

	/**
	 * Set an outgoing request data variable
	 * @param  string  $key    The key to set
	 * @param  string  $value  The value to set
	 * @return bool
	 */
	public static function setData($key, $value) {
		self::$output_data[$key] = $value;

		return true;
	}

	/**
	 * Set the outgoing request response code
	 * @param  string  $status "error" or "success"
	 * @param  string  $message Human readable response message (optional)
	 * @return bool
	 */
	public static function setResponseCode($status, $message = "") {
		self::$response_code = array("status" => $status, "message" => $message);
		return true;
	}

	/**
	 * Get the outgoing request response code
	 * @return array
	 */
	public static function getResponseCode() {
		return self::$response_code;
	}

	/**
	 * Check if this request is being served via ajax
	 * @return  bool
	 */
	public static function isAjax() {
		if(Configuration::get("dachi.api-mode", false) == true)
			return true;

		return (self::getArgument("dachi-ui", "false") == "true" || self::getArgument("radon-ui-ajax", "false") == "true");
	}

	/**
	 * Check if this request is being served via the API mode
	 * @return bool
	 */
	public static function isAPI() {
		return self::$api_mode == true;
	}

	/**
	 * Set if this request is being served via the API mode
	 * @return bool
	 */
	public static function setAPIMode($mode) {
		self::$api_mode = $mode;
	}
}

/**
 * The InvalidRequestURIException is thrown if the URI key couldn't be found or didn't match the provided regular expression.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class InvalidRequestURIException extends \Dachi\Core\Exception { }

/**
 * The InvalidRequestArgumentException is thrown if the argument key couldn't be found or didn't match the provided regular expression.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class InvalidRequestArgumentException extends \Dachi\Core\Exception { }
