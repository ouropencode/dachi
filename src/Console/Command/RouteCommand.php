<?php
namespace Dachi\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RouteCommand extends Command
{
	protected $routes = array();

	protected function configure()
	{
		$this->setName('dachi:route')
			->setDescription('Generate routing information for Dachi');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln("Loading controller classes...");

		$paths = array_merge(
			array("src", "vendor"),
			array_filter(glob('src-*'), 'is_dir')
		);
		foreach($paths as $path)
			$this->processFolder($path);

		$output->writeln("Detecting Dachi controllers...");

		$controllers = $this->detectControllers();

		foreach($controllers as $controller)
			$this->addRoute($controller, $output);

		if(!file_exists('cache'))
			mkdir('cache');

		file_put_contents('cache/dachi.routes.ser', serialize($this->routes));

		$output->writeln("Done!");
    return 0;
	}

	protected function processFolder($directory) {
		if(!$directory || $directory == "/" || $directory == "\\" || !file_exists($directory))
			return false;

		$files = array_diff(scandir($directory), array('.', '..'));

		foreach($files as $file) {
			(is_dir($directory . "/" . $file)) ? $this->processFolder($directory . "/" . $file) : $this->loadFile($directory . "/" . $file);
		}

		return true;
	}

	protected function loadFile($file) {
		if(substr($file, -4) !== ".php" || strpos($file, "Controller") === false)
			return false;

		if(strpos($file, "vendor/ouropencode/dachi/example") === 0)
			return false;

		if(strpos($file, "vendor/composer/") === 0)
			return false;

		require_once $file;
	}

	protected function detectControllers() {
		$classes = get_declared_classes();

		$controllers = array();

		foreach($classes as $class) {
			$reflect = new \ReflectionClass($class);
			if($reflect->isSubclassOf('Dachi\Core\Controller')) {
				$methods = $reflect->getMethods();
				foreach($methods as $method) {
					$doc_block = $method->getDocComment();
					if(preg_match("/@route-url\s+([^$\n\r]+)/i", $doc_block, $matches)) {
						$controller = array(
							"class"       => '\\' . $class,
							"method"      => $method->getName(),
							"route"       => $matches[1],
							"api-mode"    => false,
							"session"     => false,
							"nonce"       => false,
							"nonce-check" => false
						);

						if(preg_match("/@route-render\s+([^$\n\r]+)/i", $doc_block, $matches))
							$controller["render-path"] = $matches[1];

						if(preg_match("/@api/i", $doc_block, $matches))
							$controller["api-mode"] = true;

						if(preg_match("/@session/i", $doc_block, $matches))
							$controller["session"] = true;

						if(preg_match("/@nonce[^-]/i", $doc_block, $matches))
							$controller["nonce"] = true;

						if(preg_match("/@nonce-check\s+([^$\n\r]+)/i", $doc_block, $matches))
							$controller["nonce-check"] = $matches[1];

						$controllers[] = $controller;
					}
				}
			}
		}

		return $controllers;
	}

	protected function addRoute($controller, $output) {
		$class       = $controller["class"];
		$method      = $controller["method"];
		$route       = $controller["route"];
		$api_mode    = $controller["api-mode"];
		$session     = $controller["session"];
		$nonce       = $controller["nonce"];
		$nonce_check = $controller["nonce-check"];

		$route = str_replace("\\", "/", $route);
		$route = preg_replace("/\s+/", "", $route);
		if($route[0] == "/")
			$route = substr($route, 1);

		if($route[strlen($route) - 1] == "/")
			$route = substr($route, 0, -1);

		$output->writeln(
				"Adding " . ($api_mode ? "API " : "") . "route '/" . $route . "'" .
					($session ? " with session unclosed" : "") .
					($nonce ? " with nonce" : "") .
					($nonce_check ? " with nonce check" : "") .
				".");

		$routeParts = explode('/', $route);

		$position = &$this->routes;
		$count = count($routeParts);
		$variables = array();

		for($i = 0; $i < $count; $i++) {
			$part = $routeParts[$i];

			if(strlen($part) > 0 && $part[0] == ":") {
				$variables[] = array($i, substr($part, 1));
				$part = "*";
			}

			if($i == $count - 1) {
				if(isset($position[$part]["route"])) {
					$reflectNew = new \ReflectionClass($class);
					$reflectNewMethod = $reflectNew->getMethod($method);
					$output->writeln("");
					$output->writeln("<error>ERROR: URL ROUTING COLLISION -------------------------</error>");
					$output->writeln("Class:  " . $class);
					$output->writeln("Method: " . $method);
					$output->writeln("Route:  " . $route);
					$output->writeln("File:   " . $reflectNewMethod->getFileName() . ":" . $reflectNewMethod->getStartLine());

					$reflectExisting = new \ReflectionClass($position[$part]["route"][0]);
					$reflectExistingMethod = $reflectExisting->getMethod($position[$part]["route"][1]);
					$output->writeln("");
					$output->writeln("<error>THIS URL ROUTE IS PREVIOUSLY REGISTERED --------------</error>");
					$output->writeln("Existing Class:  " . $position[$part]["route"][0]);
					$output->writeln("Existing Method: " . $position[$part]["route"][1]);
					$output->writeln("Existing Route:  " . $position[$part]["route"][2]);
					$output->writeln("Existing File:   " . $reflectExistingMethod->getFileName() . ":" . $reflectExistingMethod->getStartLine());
					exit(101);
				}
				$final_route = array(
					"class"     => $class,
					"method"    => $method,
					"route"     => $route,
					"variables" => $variables
				);

				if(isset($controller["render-path"])) {
					$path = str_replace("\\", "/", $controller["render-path"]);
					$path = preg_replace("/\s+/", "", $path);
					if($path[0] == "/")
						$path = substr($path, 1);

					if($path[strlen($path) - 1] == "/")
						$path = substr($path, 0, -1);

					$final_route["render-path"] = $path;
				}

				if($api_mode)    $final_route["api-mode"] = true;
				if($session)     $final_route["session"] = true;
				if($nonce)       $final_route["nonce"] = true;
				if($nonce_check) $final_route["nonce_check"] = true;

				$position[$part]["route"] = $final_route;
			}

			$position = &$position[$part]["children"];
		}
	}
}
