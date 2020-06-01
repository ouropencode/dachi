<?php
namespace Dachi\Tests;

use Dachi\Core\Exception;

class ExceptionTest extends Dachi_TestBase {
	public function testDachiException() {
		$this->assertInstanceOf('\Dachi\Core\Exception', new \Dachi\Core\Exception);
	}
}