<?php
namespace Dachi\Core\Radon;

use Dachi\Core\Controller;
use Dachi\Core\Request;
use Dachi\Core\Modules;

/**
 * The ControllerRadon class is responsable for providing uncompiled templates to Radon-UI.
 *
 * These templates are served via the /__tpl/:template route and provided for Radon-UI to render them using Twig.JS
 * The :template URL argument is formatted like the same as in Dachi controllers, with all slashes replaced with $@$
 * i.e:   @SampleModule$@$my-template-file
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class ControllerRadon extends Controller {
	/**
	 * @route-url /__tpl/:template
	 **/
	public function get_template() {
		$template = str_replace("$@$", "/", Request::getUri("template"));

		foreach(Modules::getAll() as $module) {
			if(strpos($template, "@" . $module->getShortName()) !== false) {
				$basePath = $module->getPath();

				$template = realpath(str_replace("@" . $module->getShortName(), $basePath . "/views", $template) . ".twig");
				if(substr($template, 0, strlen($basePath)) !== $basePath)
					throw new TemplateNotFoundException;

				$file = fopen($template, 'rb');
				fpassthru($file);
				exit;
			}

			if(strpos($template, "@global") !== false) {
				$basePath = realpath("views");
				$template = realpath(str_replace("@global", $basePath, $template) . ".twig");
				if(substr($template, 0, strlen($basePath)) !== $basePath)
					throw new TemplateNotFoundException;

				$file = fopen($template, 'rb');
				fpassthru($file);
				exit;
			}
		}

		throw new TemplateNotFoundException;
		exit;
	}
}

/**
 * The TemplateNotFoundException is thrown if a template file could not be found.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class TemplateNotFoundException extends \Dachi\Core\Exception { }
