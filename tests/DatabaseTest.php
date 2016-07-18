<?php
namespace Dachi\Tests;

use Dachi\Core\Database;

class DatabaseTest extends Dachi_TestBase {
	public function testGetEntityManager() {
		$this->assertInstanceOf('\Doctrine\ORM\EntityManager', Database::getEntityManager());
	}

	public function testGetRepository() {
		include_once "src/UnitTestModuleA/RepositoryTest.php";
		include_once "src/UnitTestModuleA/ModelTest.php";

		$this->assertInstanceOf('\UnitTestNamespace\UnitTestModuleA\RepositoryTest', Database::getRepository("UnitTestModuleA:ModelTest"));
	}

	public function testWrapperMethods() {
		$this->assertEquals(true, method_exists("\Dachi\Core\Database", "find"));
		$this->assertEquals(true, method_exists("\Dachi\Core\Database", "flush"));
		$this->assertEquals(true, method_exists("\Dachi\Core\Database", "persist"));
		$this->assertEquals(true, method_exists("\Dachi\Core\Database", "remove"));
		$this->assertEquals(true, method_exists("\Dachi\Core\Database", "createQuery"));
	}
}