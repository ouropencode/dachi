<?php
namespace Dachi\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{
	protected function configure()
	{
		$this->setName('dachi:create')
			->setDescription('Generate a new Dachi project')
			->addArgument(
				'destination',
				InputArgument::OPTIONAL,
				'Destination folder to generate project in'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$helper = $this->getHelper('question');

		$destination = '.';
    	$inputDestination = $input->getArgument('destination');
    	if($inputDestination)
    		$destination = $inputDestination;

		$output->writeln("Copying project files...");

		$this->copyFiles(__DIR__ . "/../../../example", $destination);

		chdir($destination);

		$cmd = $this->getApplication()->find("dachi:all");
		$cmd_input = new ArrayInput(array("dachi:all"));
		$cmd->run($cmd_input, $output);

		$output->writeln("Done!");
	}

	protected function copyFiles($source, $destination) {	
		$dir = opendir($source); 
		@mkdir($destination); 
		while(($file = readdir($dir)) !== false) {
			if ($file != '.' && $file != '..') {
				if (is_dir($source . '/' . $file)) {
					@mkdir($destination . '/' . $file);
					$this->copyFiles($source . '/' . $file, $destination . '/' . $file); 
				} else {
					copy($source . '/' . $file, $destination . '/' . $file); 
				} 
			} 
		} 
		closedir($dir); 
	}
}