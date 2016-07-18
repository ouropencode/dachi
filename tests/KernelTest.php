<?php
namespace Dachi\Tests;

use Dachi\Core\Kernel;

class KernelTest extends Dachi_TestBase {
	public function testInitialize() {
		Kernel::initialize();
		$this->assertEquals(DACHI_KERNEL, true);
	}

	public function testGetEnvironment() {
		$this->assertEquals("local", Kernel::getEnvironment());

		file_put_contents("dachi_environment", "production");
		$this->assertEquals("production", Kernel::getEnvironment(true));

		file_put_contents("dachi_environment", "development");
		$this->assertEquals("development", Kernel::getEnvironment(true));

		file_put_contents("dachi_environment", "local");
		$this->assertEquals("local", Kernel::getEnvironment(true));
	}

	public function testGetVersion() {
		$version = Kernel::getVersion();
		$this->assertStringMatchesFormat('%i.%i', Kernel::getVersion(),
			"Short kernel version did not fit format.  Expected: `%i.%i`  Received: `" . $version . "`"
		);

		$version = Kernel::getVersion(true);
		$this->assertStringMatchesFormat('v%i.%i.%i', Kernel::getVersion(true),
			"Full kernel version did not fit format.  Expected: `v%i.%i.%i`  Received: `" . $version . "`"
		);
	}

}