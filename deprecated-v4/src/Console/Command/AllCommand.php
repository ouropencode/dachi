<?php
namespace Dachi\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AllCommand extends Command
{
	protected function configure()
	{
		$this->setName('dachi:all')
			->setDescription('Perform all routing operations to update Dachi cache files');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$commands = array(
			"dachi:config"  => array(),
			"dachi:route"   => array(),
			"dachi:modules" => array()
		);

		foreach($commands as $command => $arguments) {
			$cmd = $this->getApplication()->find($command);
			$output->writeln("--------------------------------------------------[ " . $command);
			array_unshift($arguments, $command);
			$cmd_input = new ArrayInput($arguments);
			$cmd->run($cmd_input, $output);
		}
		
		$output->writeln("--------------------------------------------------[ DONE! DONE!");
	}
}
