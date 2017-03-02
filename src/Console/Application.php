<?php

namespace Dachi\Core\Console;

use Dachi\Core\Kernel;
use Symfony\Component\Console\Application as CLIApplication;

/**
 * The Application class is responsable for managing the CLI for Dachi.
 *
 * @version   2.0.0
 *
 * @since     2.0.0
 *
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class Application
{
    /**
     * Create the CLI application.
     *
     * @return null
     */
    public static function create()
    {
        $app = new CLIApplication('Dachi', Kernel::getVersion(true));

        $commands = [
            new \Dachi\Core\Console\Command\TestCommand(),
            new \Dachi\Core\Console\Command\CreateCommand(),
            new \Dachi\Core\Console\Command\DocumentCommand(),

            /* run under 'dachi:all' **/
            new \Dachi\Core\Console\Command\RouteCommand(),
            new \Dachi\Core\Console\Command\ModulesCommand(),
            new \Dachi\Core\Console\Command\ConfigCommand(),

            new \Dachi\Core\Console\Command\AllCommand(),
            /* end run under 'dachi:all' **/
        ];

        foreach ($commands as $commandClass) {
            $app->add($commandClass);
        }

        $app->run();
    }
}
