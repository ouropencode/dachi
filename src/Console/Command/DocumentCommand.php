<?php
namespace Dachi\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DocumentCommand extends Command
{
	protected function configure()
	{
		$this->setName('dachi:document')
			->setDescription('Generate documentation for Dachi')
			->addArgument(
				'destination',
				InputArgument::OPTIONAL,
				'Destination folder to generate documentation in'
			)
			->addOption(
				'internal',
				null,
				InputOption::VALUE_NONE,
				'If set, documentation will be generated with additional information intended only for internal developers'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		if($input->getOption('internal')) {
			$type = "internal";
			$destination = 'documentation/internal';
			$access_levels = '["public","protected","private"]';
			$extra_flags = "--internal --todo";
		} else {
			$type = "public";
			$destination = 'documentation/public';
			$access_levels = '["public"]';
			$extra_flags = "--no-source-code";
		}

		$inputDestination = $input->getArgument('destination');
		if($inputDestination)
			$destination = $inputDestination;

		$output->writeln("Generating " . $type . " documentation in '" . $destination . "'...");

		$this->deleteTree($destination);

		$flags = "--template-theme=bootstrap --deprecated --download " . $extra_flags;

		$DS = function($x) { return implode(DIRECTORY_SEPARATOR, $x); };
		if(file_exists($DS(array("vendor","ld-packages","dachi")))) {
			$response = shell_exec($DS(array("vendor","bin","apigen")) . " generate -s src -s " . $DS(array("vendor","ld-packages","dachi","src")) . " -d " . $destination . " " . $flags . " --title Dachi");
		} else {
			$response = shell_exec($DS(array("vendor","bin","apigen")) . " generate -s src -d " . $destination . " " . $flags . " --title Dachi");
		}
		$output->writeln($response);

		$output->writeln("Copying licence file...");
		copy(__DIR__ . "/../../../LICENCE.md", $destination . "/LICENCE.md");

		$output->writeln("Done!");
    return 0;
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
