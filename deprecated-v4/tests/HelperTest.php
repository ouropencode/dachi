<?php
namespace Dachi\Tests;

use Dachi\Core\Helper;

class HelperTest extends Dachi_TestBase {
	public function testHelperParentExists() {
		$this->assertEquals(true, class_exists('\Dachi\Core\Helper'));
	}
}