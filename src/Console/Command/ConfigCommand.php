<?php
namespace Dachi\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigCommand extends Command
{
  protected function configure()
  {
    $this->setName('dachi:config')
      ->setDescription('Generate configuration file for Dachi');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $output->writeln("Generating configuration file...");

    $this->buildConfiguration();

    $output->writeln("Done!");
  }

  protected function buildConfiguration() {
    $config = array();

    $environments = array("production", "development", "local", "testing");
    while(count($environments) > 0) {
      $path = 'config/' . $environments[0] . '/';
      if(file_exists($path) && $handle = opendir($path)) {
        while(false !== ($entry = readdir($handle))) {
          if($entry == "." || $entry == "..") continue;

          foreach($environments as $env) {
            $position = &$config[$env];
            $key = explode(str_replace('.json', '', $entry), '.');

            $token = strtok(str_replace('.json', '', $entry), '.');
            while($token !== false) {
              $nextToken = strtok('.');
              if(!isset($position[$token]))
                $position[$token] = array();

              if($nextToken === false) {
                $position[$token] = json_decode(file_get_contents($path . $entry), true);
              } else {
                $position = &$position[$token];
              }

              $token = $nextToken;
            }
          }
        }
      }
      unset($environments[0]);
      $environments = array_values($environments);
    }

    if(!file_exists('cache'))
      mkdir('cache');

    file_put_contents('cache/dachi.config.ser', serialize($config));
  }
}
