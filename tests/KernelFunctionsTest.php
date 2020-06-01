<?php
namespace Dachi\Tests;

use Dachi\Core\Kernel;

class KernelFunctionsTest extends Dachi_TestBase {
	public function testUnparseUrl() {
		Kernel::initialize();

		$url = "https://user:pass@host:1337/with/path?and=multiple&url=arguments";
		$parsed_url = parse_url($url);
		$this->assertEquals($url, unparse_url($parsed_url));

		$parsed_url = array(
			"scheme" => "https",
			"host"   => "host",
			"port"   => "1337",
			"user"   => "user",
			"pass"   => "pass",
			"path"   => "/with/path",
			"query"  => array(
				"and" => "multiple",
				"url" => "arguments"
			)
		);
		$this->assertEquals($url, unparse_url($parsed_url));
	}

	public function testArrayColumn() {
		Kernel::initialize();

		$data = array(
			array("id" => 123, "name" => "abc"),
			array("id" => 456, "name" => "def"),
			array("id" => 789, "name" => "ghi"),
		);

		$this->assertEquals(array(123, 456, 789),       array_column($data, "id"));
		$this->assertEquals(array("abc", "def", "ghi"), array_column($data, "name"));
	}

	public function testGetExceutionTime() {
		Kernel::initialize();

		$firstTime = getExecutionTime();
		sleep(1);
		$secondTime = getExecutionTime();
		sleep(1);
		$thirdTime = getExecutionTime();

		$this->assertGreaterThan($firstTime, $secondTime);
		$this->assertGreaterThan($secondTime, $thirdTime);
	}

	public function testCurlGetContents() {
		Kernel::initialize();

		$response = curl_get_contents("http://postman-echo.com/get", array(), array(
			CURLOPT_VERBOSE => false
		));

    $parsed = json_decode($response);
		$this->assertEquals('http://postman-echo.com/get', $parsed->url);
	}

	public function testCurlGetContentsSsl() {
		Kernel::initialize();

		$response = curl_get_contents("https://postman-echo.com/get", array(), array(
			CURLOPT_VERBOSE => false
		));

    $parsed = json_decode($response);
		$this->assertEquals('https://postman-echo.com/get', $parsed->url);
	}

	public function testJsonEcho() {
		Kernel::initialize();

		ob_start();
		json_echo(array("test" => "value"));
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals("{\"test\":\"value\"}", $output);
	}

	public function testGetCallingNamespace() {
		Kernel::initialize();

		$namespace = get_calling_namespace();
		$this->assertEquals('PHPUnit\Framework', $namespace);

		$namespace = $this->__getThisCallingNamespace();
		$this->assertEquals('Dachi\Tests', $namespace);
	}

	private function __getThisCallingNamespace() {
		return get_calling_namespace();
	}
}
