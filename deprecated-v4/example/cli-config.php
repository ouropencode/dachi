<?php
use Dachi\Core\Kernel;
use Dachi\Core\Database;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

define('DACHI_CLI', true);
Kernel::initialize();

$entity_manager = Database::getEntityManager();
return ConsoleRunner::createHelperSet($entity_manager);