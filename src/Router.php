<?php
namespace Dachi\Core;

/**
 * The Router class is responsable for routing all page requests through to the controller.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    $ourOpenCode
 */
class Router {
	protected static $routes = array();

	/**
	 * Load the routing information object into memory.
	 * @return null
	 */
	protected static function load() {
		if(file_exists('cache/dachi.routes.json'))
			self::$routes = json_decode(file_get_contents('cache/dachi.routes.json'), true);
	}

	/**
	 * Performs routing based upon the loaded routing information and the incoming request
	 * @return null
	 */
	public static function route() {
		if(defined('DACHI_CLI'))
			return false;

		$uri = Request::getFullUri();
		$route = self::findRoute($uri);
		self::performRoute($route);

		return Template::render();
	}

	/**
	 * Find and process a valid route from the uri
	 * @internal
	 * @param  array $uri Array of uri parts (split by /)
	 * @throws ValidRouteNotFoundException
	 * @return array
	 */
	public static function findRoute($uri) {
		if(self::$routes === array())
			self::load();

		$count = count($uri);

		$position = &self::$routes;
		for($i = 0; $i < $count; $i++) {
			if($i == $count - 1) {
				if(isset($position[$uri[$i]]) && isset($position[$uri[$i]]["route"])) {
					return $position[$uri[$i]]["route"];
				} else if(isset($position["*"]) && isset($position["*"]["route"])) {
					return $position["*"]["route"];
				} else {
					throw new ValidRouteNotFoundException;
				}
			} else {
				if(isset($position[$uri[$i]])) {
					$position = &$position[$uri[$i]]["children"];
				} elseif(isset($position["*"])) {
					$position = &$position["*"]["children"];
				} else {
					throw new ValidRouteNotFoundException;
				}
			}
		}

		throw new ValidRouteNotFoundException;
	}

	/**
	 * Perform routing based upon the discovered route
	 * @internal
	 * @param  array $route Format: array(class, method, uri_variables)
	 * @see Request::setRequestVariables()
	 * @return mixed Return value of last route to be executed
	 */
	public static function performRoute($route) {
		$api_mode = isset($route["api-mode"]);
		Request::setRequestVariables($route["variables"], $api_mode);

		$controller = new $route["class"];
		$response = $controller->$route["method"]();

		if(!Request::isAjax() && !Request::isAPI() && isset($route["render-path"])) {
			Request::setRenderPath($route["render-path"]);
			try {
				$route = self::findRoute(explode('/', $route["render-path"]));
			} catch(ValidRouteNotFoundException $e) {
				throw new ValidRenderRouteNotFoundException($e);
			}
			$response = self::performRoute($route);
		}

		return $response;
	}
}

/**
 * The ValidRouteNotFoundException is thrown if the url has no route registered.
 *
 * This means there is no Controller that has provided a @url-route for this url
 * or the dachi.routes.json cache file is out dated and needs regenerating.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class ValidRouteNotFoundException extends \Dachi\Core\Exception { }
class ValidRenderRouteNotFoundException extends \Dachi\Core\Exception { }
