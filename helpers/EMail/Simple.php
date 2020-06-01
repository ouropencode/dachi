<?php
namespace Dachi\Helpers\EMail;

use Dachi\Core\Configuration;
use Dachi\Core\Template;
use Dachi\Core\Kernel;

class Simple extends \Dachi\Helpers\EMail {

	public static function send($options, $extra_options = array()) {
	}

	private static function tempdir($dir = null, $prefix = 'tmp_', $mode = 0700, $maxAttempts = 1000) {
	}

}

class PHPMailerException extends \Exception { }
