<?php
namespace Dachi\Core;

/**
 * This is our Kernel.
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
class Logger
{
	protected static $log = array();
	protected static $error_count = 0;

	public static function initialize() {
		set_error_handler(array('\Dachi\Core\Logger', 'handle_error'));
		register_shutdown_function(array('\Dachi\Core\Logger', 'handle_shutdown'));

		ini_set('display_errors', 0);
	}

	/**
	 * This function is called when PHP exits, it is used to handle any errors
	 * and report information back to Filament (the error reporting tool). It
	 * is unadivsed to call this function manually.
	 *
	 * @internal
	 * @return bool
	 */
	public static function handle_shutdown() {
		$isFatal = false;
		$error = error_get_last();
		if($error !== NULL && $error['type'] === E_ERROR) {
			$isFatal = true;
			self::handle_error($error['type'], $error['message'], $error['file'], $error['line']);
		}

		if(!count(self::$log))
			return false;

		$filament = self::getFilament();

		curl_get_contents(Configuration::get("filament.location"), array(
			"version"     => 2,
			"key"         => Configuration::get("filament.key"),
			"data"        => @json_encode($filament),
			"error_count" => self::$error_count
		), defined('CURLOPT_CONNECTTIMEOUT_MS') ? array(
			CURLOPT_NOSIGNAL => true,
			CURLOPT_CONNECTTIMEOUT_MS => 250
		) : array(
			CURLOPT_CONNECTTIMEOUT => 1
		));

		if($isFatal) {
			Request::setResponseCode("exception", "fatal server error");
			$response = array(
				"data"           => $data,
				"response"       => Request::getResponseCode(),
				"render_actions" => array()
			);

			if(Request::isAjax()) {
				http_response_code(200);
				if(Kernel::getEnvironment() != "production")
					$response["filament"] = $filament;
				json_echo($response);
			} else {
				echo "<h1>Fatal Error</h1>";
				if(Kernel::getEnvironment() != "production")
					echo "<pre>" . var_export(self::$log, true) . "</pre>";
			}
		}
	}

	/**
	 * This function is called when a PHP error occurs. It is unadivsed to call
	 * this function manually.
	 *
	 * @internal
	 * @param int     $errno   The error number
	 * @param string  $errstr  The error message
	 * @param string  $errfile The file that caused the error
	 * @param int     $errline The line that caused the error
	 * @return bool
	 */
	public static function handle_error($errno, $errstr, $errfile, $errline) {
		if (!(error_reporting() & $errno))
			return false;

		self::error(sprintf("%s [%s] %s. Line: %s. File: %s", human_error_code($errno), $errno, $errstr, $errline, $errfile));
		return true;
	}

	public static function getFilament() {
		$request = array(
			"uri"         => Request::getFullUri(),
			"render_path" => Request::getRenderPath(),
			"data"        => Request::getAllData(),
			"ajax"        => Request::isAjax()
		);

		return array(
			"environment" => Kernel::getEnvironment(),
			"log"         => self::$log,
			"time"        => time(),
			"request_id"  => md5(time() . rand() . session_id()),
			"session_id"  => session_id(),
			"request"     => json_encode($request)
		);
	}

	/**
	 * Add a log entry to the filament log. This log is sent to the filament
	 * server after script execution.
	 *
	 * @internal
	 * @param string  $type      The log message.
	 * @param array   $arguments An array of the log arguments.
	 * @return bool
	 */
	private static function filament($type, $arguments) {
		$uniqId = md5($type . json_encode($arguments));
		if(isset(self::$log[$uniqId]))
			return ++self::$log[$uniqId]["count"];

		self::$log[$uniqId] = array(
			"count"     => 1,
			"type"      => $type,
			"arguments" => $arguments
		);

		return true;
	}


	/**
	 * Trigger an error message. Supply as many arguments as you please, of any
	 * type, and they will be recorded.
	 */
	public static function error() {
		self::$error_count++;
		self::filament("error", func_get_args());
	}


	/**
	 * Trigger an info message. Supply as many arguments as you please, of any
	 * type, and they will be recorded.
	 */
	public static function info() {
		self::filament("info", func_get_args());
	}


	/**
	 * Trigger an warning message. Supply as many arguments as you please, of
	 * any type, and they will be recorded.
	 */
	public static function warn() {
		self::filament("warn", func_get_args());
	}


	/**
	 * Trigger an log message. Supply as many arguments as you please, of any
	 * type, and they will be recorded.
	 */
	public static function log() {
		self::filament("log", func_get_args());
	}

}
