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
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class Kernel {
	protected static $version = "3.1";
	protected static $version_patch = "26";
	protected static $environment = "";
	protected static $git_hash = "";

	/**
	 * Initialize the Dachi kernel.
	 * @return null
	 */
	public static function initialize() {
		if(defined('DACHI_KERNEL'))
			return false;

		define('DACHI_KERNEL', true);

		require_once('Kernel.functions.php');

		getExecutionTime();
		date_default_timezone_set(Configuration::get('dachi.timezone'));
		error_reporting(-1);
		define('WORKING_DIRECTORY', getcwd());

		Session::start(Configuration::get('sessions.name'), Configuration::get('sessions.domain', false), Configuration::get('sessions.secure', false));
	}

	/**
	 * Get the current environment we are executing in.
	 *
	 * This is only available if running via the internal LemonDigits.com build toolkit, or if
	 * your CI can be configured to output the environment to the 'dachi_environment' file.
	 * (this could also be done manually by a developer during deployment)
	 *
	 * @param bool $forceCheck Should we ignore the cached environment and forceably check the filesystem?
	 * @return string
	 */
	public static function getEnvironment($forceCheck = false) {
		if(self::$environment && !$forceCheck)
			return self::$environment;

		self::$environment = "local";

		if(file_exists("dachi_environment"))
			self::$environment = trim(file_get_contents("dachi_environment"));

		return self::$environment;
	}

	/**
	 * Get the current short git hash for the revision of code we are executing.
	 *
	 * This is only available if running via the internal LemonDigits.com build toolkit, or if
	 * your CI can be configured to output the short hash (6-chars) to the 'dachi_git_hash' file
	 * (this could also be done manually by a developer during deployment)
	 *
	 * @param bool $forceCheck Should we ignore the cached git hash and forceably check the filesystem?
	 * @return string
	 */
	public static function getGitHash($forceCheck = false) {
		if(self::$git_hash && !$forceCheck)
			return self::$git_hash;

		self::$git_hash = "ffffff";

		if(file_exists("dachi_git_hash"))
			self::$git_hash = trim(file_get_contents("dachi_git_hash"));

		return self::$git_hash;
	}

	/**
	 * Get the Dachi version.
	 * @param  bool  $fullVersion If TRUE, outputs the whole version number (including patch). If FALSE, only outputs the major version.
	 * @return string
	 */
	public static function getVersion($fullVersion = false) {
		return ($fullVersion ? ("v" . self::$version . "." . self::$version_patch) : self::$version);
	}
}
