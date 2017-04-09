<?php
namespace Dachi\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ModulesCommand extends Command
{
  protected $modules = array();
  protected $controllers = array();

  protected function configure()
  {
    $this->setName('dachi:modules')
      ->setDescription('Generate module information for Dachi');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $output->writeln("Loading controller classes...");

    $paths = array_merge(
      array("src", "vendor"),
      array_filter(glob('src-*'), 'is_dir')
    );
    foreach($paths as $path)
      $this->processFolder($path);

    $output->writeln("Detecting Dachi namespaces...");

    $this->detectNamespaces();

    foreach($this->modules as $module)
      $this->addNamespace($module, $output);

    if(!file_exists('cache'))
      mkdir('cache');

    file_put_contents('cache/dachi.modules.ser', serialize($this->modules));

    foreach($this->controllers as $controller)
      $this->runSetup($controller, $output);

    $output->writeln("Done!");
  }

  protected function processFolder($directory) {
    if(!$directory || $directory == "/" || $directory == "\\" || !file_exists($directory))
      return false;
    
    if (substr($directory, -18) == "vendor/maxmckenzie") return false;

    $files = array_diff(scandir($directory), array('.', '..'));

    foreach($files as $file) {
      (is_dir($directory . "/" . $file)) ? $this->processFolder($directory . "/" . $file) : $this->loadFile($directory . "/" . $file);
    }

    return true;
  }

  protected function loadFile($file) {
    if(substr($file, -4) !== ".php")
      return false;

    if(strpos($file, "Controller") === false && strpos($file, "Model") === false)
      return false;

    if(strpos($file, "vendor/ouropencode/dachi/example") === 0)
      return false;

    require_once $file;
  }

  protected function detectNamespaces() {
    $classes = get_declared_classes();

    foreach($classes as $class) {
      $reflect = new \ReflectionClass($class);
      if($reflect->isSubclassOf('Dachi\Core\Controller') || $reflect->isSubclassOf('Dachi\Core\Model')) {
        $namespace = $reflect->getNamespaceName();
        $split_namespace = explode('\\', $namespace);

        if(count($split_namespace) > 1) {
          $module = array();
          $module["namespace"] = $namespace;
          $module["shortname"] = $split_namespace[count($split_namespace) - 1];
          $module["path"]      = dirname($reflect->getFileName());

          $this->modules[$module["namespace"]] = $module;
          $this->controllers[] = $class;
        }
      }
    }
  }

  protected function runSetup($controller, $output) {
    $reflect = new \ReflectionClass($controller);
    if($reflect->hasMethod('__setup')) {
      $output->writeln("Running setup for '" . $controller);
      try {
        $instance = new $controller();
        $instance->__setup();
      } catch (Exception $e) {
        $output->writeln("Error in setup:");
        $output->writeln($e);
      }
    }
  }

  protected function addNamespace($module, $output) {
    $output->writeln("Adding module '" . $module["shortname"] . "' from '" . $module["namespace"] . "'.");

    $shortname = $module["shortname"];
    if(isset($this->modules[$shortname])) {
      $output->writeln("<error>THIS MODULE NAME IN USE BY ANOTHER MODULE --------------</error>");
      $output->writeln("Shortname:         " . $shortname);
      $output->writeln("In Use By:         " . $this->modules[$shortname]["namespace"]);
      $output->writeln("Attempted Use By:  " . $module["namespace"]);
      exit(101);
    }
    $this->modules[$shortname] = $module;
  }
}
