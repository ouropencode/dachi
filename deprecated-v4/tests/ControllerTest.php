<?php
namespace Dachi\Tests;

use Dachi\Core\Controller;

class ControllerTest extends Dachi_TestBase {
	public function testControllerParentExists() {
		$this->assertEquals(true, class_exists('\Dachi\Core\Controller'));
	}
}