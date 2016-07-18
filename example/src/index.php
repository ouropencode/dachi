<?php
chdir('..');
require_once __DIR__ . '/../vendor/autoload.php';

use Dachi\Core\Kernel;
use Dachi\Core\Router;

Kernel::initialize();
Router::route();