<?php
namespace Dachi\Helpers\EMail;

use Dachi\Core\Configuration;
use Dachi\Core\Template;
use Dachi\Core\Kernel;

class MailGun extends \Dachi\Helpers\EMail {
	protected static $mailgun = null;

	public static function initalize() {
	}

	public static function send($options, $mailgun_options = array()) {
	}

	public static function getMailgun() {
	}

	private static function tempdir($dir = null, $prefix = 'tmp_', $mode = 0700, $maxAttempts = 1000) {
	}
}
