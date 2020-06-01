<?php
namespace Dachi\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
	protected function configure()
	{
		$this->setName('dachi:test')
			->setDescription('Run dachi unit tests')
			->addOption(
				'tap',
				null,
				InputOption::VALUE_NONE,
				'If set, phpunit will output very detailed information'
			)
			->addOption(
				'testdox',
				null,
				InputOption::VALUE_NONE,
				'If set, phpunit will output more detailed information'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("--------------------------------------------------[ DACHI UNIT TESTS");

		$DS = function($x) { return implode(DIRECTORY_SEPARATOR, $x); };

		$tests_dachi    = realpath(__DIR__ . "/../../../tests");
		$tests_project = realpath(PATH_TO_PROJECT_ROOT . "/tests");

		if(file_exists($tests_dachi . "/.test-temp/"))
			$this->deleteTree($tests_dachi . "/.test-temp/");
		
		if(file_exists($tests_project . "/.test-temp/"))
			$this->deleteTree($tests_project . "/.test-temp/");

		$args_dachi = " --process-isolation --colors=auto -d include_path=\".\" " . ($input->getOption('tap') ? "--tap" : "") . " " . ($input->getOption('testdox') ? "--testdox" : "");
		$args_project = " --process-isolation --colors=auto -d include_path=\".\" " . ($input->getOption('tap') ? "--tap" : "") . " " . ($input->getOption('testdox') ? "--testdox" : "");

		$bootstrap = "--bootstrap " . $tests_dachi . "/Dachi_TestBase.php";
		exec($x = $DS(array("vendor","bin","phpunit")) . " " . $bootstrap . " " . $args_dachi . " " . $tests_dachi, $response, $return_val);
		$output->writeln(implode("\n", $response));
		if($return_val !== 0) {
			$output->writeln("Dachi unit tests failed!");
			exit(101);
		}

		if(file_exists($tests_project) && $tests_project != $tests_dachi) {
			$output->writeln("--------------------------------------------------[ PROJECT UNIT TESTS");

			$bootstrap = "";
			if(file_exists($tests_project . "/bootstrap.php"))
				$bootstrap = "--bootstrap " . $tests_project . "/bootstrap.php ";

			exec($DS(array("vendor","bin","phpunit")) . " " . $bootstrap . " " . $args_project . " " . $tests_project, $response_proj, $return_val_proj);
			$output->writeln($response_proj);
			if($return_val_proj !== 0) {
				$output->writeln("Project unit tests failed!");
				exit(102);
			}
		}

		if(file_exists($tests_dachi . "/.test-temp/"))
			$this->deleteTree($tests_dachi . "/.test-temp/");
		
		if(file_exists($tests_project . "/.test-temp/"))
			$this->deleteTree($tests_project . "/.test-temp/");

		$output->writeln("Done!");
	}

	protected function deleteTree($directory) {
		if(!$directory || $directory == "/" || $directory == "\\" || !file_exists($directory))
			return false;

		$files = array_diff(scandir($directory), array('.', '..')); 

		foreach ($files as $file) { 
			(is_dir("$directory/$file")) ? $this->deleteTree("$directory/$file") : unlink("$directory/$file"); 
		}

		return rmdir($directory); 
	}
}