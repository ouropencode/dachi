<?php

use Dachi\Core\Database;
use Dachi\Core\Kernel;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

define('DACHI_CLI', true);
Kernel::initialize();

$entity_manager = Database::getEntityManager();

return ConsoleRunner::createHelperSet($entity_manager);
