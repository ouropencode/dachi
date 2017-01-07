<?php
namespace Dachi\Core;

/**
 * The Template class is responsable for rendering all templates.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    $ourOpenCode
 */
class Template {
	protected static $twig            = null;
	protected static $render_actions  = array();
	protected static $render_template = '@global/base';

	/**
	 * Load the routing information object into memory.
	 * @return null
	 */
	protected static function initialize() {
		$loader = new \Twig_Loader_Filesystem();

		foreach(Modules::getAll() as $module)
			if(file_exists($module->getPath() . '/Views'))
				$loader->addPath($module->getPath() . '/Views', $module->getShortName());

		if(file_exists('views')) {
			$loader->addPath('views', 'global');
			$loader->addPath('views', 'Global');
		}

		self::$twig = new \Twig_Environment($loader, array(
			'debug'            => Configuration::get('debug.template', 'false') === 'true',
			'auto_reload'      => Kernel::getEnvironment() === "local",
			'charset'          => Configuration::get('templates.charset', 'utf-8'),
			'cache'            => 'cache/twig',
			'strict_variables' => false,
			'autoescape'       => true
		));

		self::$twig->addFilter(new \Twig_SimpleFilter('time_short', function($date) {
			if($date == "now") $date = new \DateTime();
			return $date->format('H:i');
		}));
		self::$twig->addFilter(new \Twig_SimpleFilter('date_short', function($date) {
			if($date == "now") $date = new \DateTime();
			return $date->format('Y-m-d');
		}));
		self::$twig->addFilter(new \Twig_SimpleFilter('date_long', function($date) {
			if($date == "now") $date = new \DateTime();
			return $date->format('jS F Y');
		}));
		self::$twig->addFilter(new \Twig_SimpleFilter('date_uk', function($date) {
			if($date == "now") $date = new \DateTime();
			return $date->format('d/m/Y');
		}));
		self::$twig->getExtension('core')->setDateFormat('Y-m-d H:i');
		
		self::$twig->addFilter(new \Twig_SimpleFilter("sortasc_*", function ($key, $data) {
			usort($data, function($a, $b) use ($key) {
				return strnatcmp($a[$key], $b[$key]);
			});
			return $data;
		}));

		self::$twig->addFilter(new \Twig_SimpleFilter("sortdesc_*", function ($key, $data) {
			usort($data, function($a, $b) use ($key) {
				return strnatcmp($b[$key], $a[$key]);
			});
			return $data;
		}));
		self::$twig->addFilter(new \Twig_SimpleFilter("abssortasc_*", function ($key, $data) {
			usort($data, function($a, $b) use ($key) {
				return strnatcmp(abs($a[$key]), abs($b[$key]));
			});
			return $data;
		}));
		self::$twig->addFilter(new \Twig_SimpleFilter("abssortdesc_*", function ($key, $data) {
			usort($data, function($a, $b) use ($key) {
				return strnatcmp(abs($b[$key]), abs($a[$key]));
			});
			return $data;
		}));
	}

	/**
	 * Retreive a twig template object
	 * @param  string $template The template file
	 * @return Twig_Template
	 */
	public static function get($template) {
		if(self::$twig === null)
			self::initialize();

		return self::$twig->loadTemplate($template . '.twig');
	}

	/**
	 * Append a template render action to the render queue
	 * @param  string $template  The template file
	 * @param  string $target_id The dachi-ui-block to load into
	 * @return null
	 */
	public static function display($template, $target_id) {
		if(self::$twig === null)
			self::initialize();

		self::$render_actions[] = array(
			"type"      => "display_tpl",
			"template"  => $template,
			"target_id" => $target_id
		);
	}

	/**
	 * Render the render queue to the browser
	 *
	 * If the request is an ajax request, the render queue and data will be sent to the browser via JSON.
	 * If the request is a standard request, the render queue will be rendered server-side and will be sent to the browser via HTML.
	 *
	 * @internal
	 * @see Router
	 * @return null
	 */
	public static function render() {
		$apiMode = Request::isAPI();

		if(self::$twig === null)
			self::initialize();

		$data = Request::getAllData();
		if(!$apiMode) {
			$data["siteName"]  = Configuration::get("dachi.siteName", "Unnamed Dachi Installation");
			$data["timezone"]  = Configuration::get("dachi.timezone", "Europe/London");
			$data["domain"]    = Configuration::get("dachi.domain", "localhost");
			$data["baseURL"]   = Configuration::get("dachi.baseURL", "/");
			$data["assetsURL"] = str_replace("%v", Kernel::getGitHash(), Configuration::get("dachi.assetsURL", "/build/"));
			$data["renderTPL"] = self::getRenderTemplate();
			$data["URI"]       = Request::getFullUri();
		}

		if($apiMode) {
			$response = array(
				"data"           => $data,
				"response"       => Request::getResponseCode()
			);
			return json_echo($response);
		} else if(Request::isAjax()) {
			$response = array(
				"render_tpl"     => self::getRenderTemplate(),
				"data"           => $data,
				"response"       => Request::getResponseCode(),
				"render_actions" => self::$render_actions
			);

			return json_echo($response);
		} else {
			$data["response"] = Request::getResponseCode();

			$response = self::$twig->render(self::getRenderTemplate(true), $data);

			foreach(array_reverse(self::$render_actions) as $action) {
				switch($action["type"]) {
					case "redirect":
						if($action["soft"] !== true)
							header("Location: " . $action["location"]);
						break;
					case "display_tpl":
						$match = preg_match("/<dachi-ui-block id=[\"']" . preg_quote($action["target_id"]) . "[\"'][^>]*>([\s\S]*)<\/dachi-ui-block>/U", $response, $matches);
						if($match) {
							$replacement = "<dachi-ui-block id='" . $action["target_id"] . "'>" . self::$twig->render($action["template"] . '.twig', $data) . "</dachi-ui-block>";
							$response = str_replace($matches[0], $replacement, $response);
						}
						break;
				}
			}

			echo $response;
		}
	}

	/**
	 * Append a redirect action to the render queue.
	 *
	 * If $location does not start with "http", the dachi.baseURL configuration value will be prepended
	 *
	 * @param  string $location The location to redirect to
	 * @return null
	 */
	public static function redirect($location, $soft = false) {
		if(substr($location, 0, 4) !== "http")
			$location = Configuration::get("dachi.baseURL") . $location;

		self::$render_actions[] = array(
			"type"     => "redirect",
			"location" => $location,
			"soft"     => $soft
		);
	}

	/**
	 * Retreive the current render queue
	 * @return array
	 */
	public static function getRenderQueue() {
		return self::$render_actions;
	}

	/**
	 * Retreive the base render template
	 * @return array
	 */
	public static function getRenderTemplate($extension = false) {
		return self::$render_template . ($extension ? ".twig" : "");
	}

	/**
	 * Set the base render template
	 * @return array
	 */
	public static function setRenderTemplate($template) {
		self::$render_template = $template;
	}

}
