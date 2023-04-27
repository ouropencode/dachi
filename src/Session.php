<?php
namespace Dachi\Core;

/**
 * The Session class is responsable for managing all session related activity
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class Session {

	private static $name;
	private static $https;
	private static $domain;
	private static $path;

	/**
	 * Start a session
	 *
	 * @param  string $name The session name
	 * @param  string $domain The domain this session is attached to. (defaults to php's SERVER_NAME)
	 * @param  boolean $forceHttps Allow session only via HTTPS (defaults to 'yes if user is currently via HTTPS')
	 * @return null
	 */
	public static function start($name, $domain = "", $forceHttps = false) {
		$domain = $domain ? $domain : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "");
		$https = $forceHttps ? true : isset($_SERVER['HTTPS']);

		if($domain == "localhost" || $domain == ".localhost")
			$domain = null;

		if(substr_count($domain, ".") < 2 && strlen($domain) >= 1 && $domain[0] != '.')
			$domain = '.' . $domain;

		self::$name   = 'sess_' . $name;
		self::$https  = $https;
		self::$domain = $domain;
		self::$path   = '/';

		if(session_status() != PHP_SESSION_NONE)
			session_destroy();

		self::session_start();

		if(!self::isValid()) {
			$_SESSION = array();
			session_destroy();
			self::session_start();
		}

		if(self::hasSessionMoved()) {
			$_SESSION = array();
			$_SESSION['dachi_agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
			self::regenerate();
		} else if (mt_rand(1, 100) <= 10) {
			self::regenerate();
		}
	}

	/**
	 * Has this session moved
	 *
	 * Moving is currently defined as "changing user agent".
	 *
	 * @return boolean
	 */
	public static function hasSessionMoved() {
		if(!isset($_SESSION['dachi_agent']))
			return true;

		$agents = $_SESSION['dachi_agent'];
		$agentc = $_SERVER['HTTP_USER_AGENT'];

		if($agents != $agentc) {
			if(strpos($agentc, "Trident") !== false && strpos($agents, "Trident") !== false)
				return false;

			return true;
		}

		return false;
	}

	/**
	 * Is this session still valid?
	 *
	 * Sessions are invalidated 10 seconds after regeneration, this is to allow AJAX
	 * requests to complete execution before terminating the session. This method will
	 * return false if the 10 second period has expired.
	 *
	 * @return boolean
	 */
	public static function isValid() {
		if(isset($_SESSION['dachi_closed']) && !isset($_SESSION['dachi_expires']))
			return false;

		if(isset($_SESSION['dachi_expires']) && $_SESSION['dachi_expires'] < time())
			return false;

		return true;
	}

	/**
	 * Regenerate the session for security
	 *
	 * There is a 10% random chance on any page request that the session ID will be regenerated
	 * and a new session identity served to the user. This prevents static session IDs that
	 * can be stolen and reused.
	 *
	 * This method sets the old session to expire in 60 seconds. This allows for any pending
	 * requests to be served before the session ID changes.
	 *
	 * @return null
	 */
	public static function regenerate() {
		if(isset($_SESSION['dachi_closed']) && $_SESSION['dachi_closed'] == true)
			return false;

		$_SESSION['dachi_closed'] = true;
		$_SESSION['dachi_expires'] = time() + 60;

		$status = session_status();
		if($status == PHP_SESSION_ACTIVE) {
			session_regenerate_id(false);

			$new_session = session_id();
			session_write_close();

			session_id($new_session);
			self::session_start();
		}

		unset($_SESSION['dachi_closed']);
		unset($_SESSION['dachi_expires']);
	}

	private static function session_start() {
		$name     = self::$name;
		$lifetime = 3600 * 2;

		session_set_cookie_params($lifetime, self::$path, self::$domain, self::$https);
		session_name($name);

		$status = session_status();
		if ($status == PHP_SESSION_DISABLED) {
			throw new Exception("sessions are disabled");
		} else if($status == PHP_SESSION_NONE) {
			if(session_start() == false)
				throw new Exception("failed to start session");
			return true;
		}

		return false;
	}

}
