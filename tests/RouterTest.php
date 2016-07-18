<?php
namespace Dachi\Tests;

use Dachi\Core\Kernel;
use Dachi\Core\Router;

class RouterTest extends Dachi_TestBase {
	public function testFindRoute() {
		$this->assertEquals(array(
			"class"     => "Dachi\\Tests\\FakeRouteProvider",
			"method"    => "getResult",
			"route"     => "__unit_test",
			"variables" => array()
		), Router::findRoute(array("__unit_test")));
	}
	public function testFindWildcardRoute() {
		$this->assertEquals(array(
			"class"     => "Dachi\\Tests\\FakeRouteProvider",
			"method"    => "getWildcardResult",
			"route"     => "__unit_test/:test_variable",
			"variables" => array(array(1, "test_variable"))
		), Router::findRoute(array("__unit_test", "wildcard")));
	}

	public function testFindWildcardChildRoute() {
		$this->assertEquals(array(
			"class"     => "Dachi\\Tests\\FakeRouteProvider",
			"method"    => "getWildcardChildResult",
			"route"     => "__unit_test/:test_variable/test",
			"variables" => array(array(1, "test_variable"))
		), Router::findRoute(array("__unit_test", "wildcard", "test")));
	}

	public function testFindNonWildcardRoute() {
		$this->assertEquals(array(
			"class"     => "Dachi\\Tests\\FakeRouteProvider",
			"method"    => "getNonWildcardResult",
			"route"     => "__unit_test/test",
			"variables" => array()
		), Router::findRoute(array("__unit_test", "test")));
	}

	public function testFindNonWildcardChildRoute() {
		$this->assertEquals(array(
			"class"     => "Dachi\\Tests\\FakeRouteProvider",
			"method"    => "getNonWildcardChildResult",
			"route"     => "__unit_test/test/test",
			"variables" => array()
		), Router::findRoute(array("__unit_test", "test", "test")));
	}

	public function testPerformRoute() {
		$_GET['dachi_uri'] = "/__unit_test/";
		$this->assertEquals("got_result", Router::performRoute(array(
			"class"     => "Dachi\\Tests\\FakeRouteProvider",
			"method"    => "getResult",
			"route"     => "__unit_test",
			"variables" => array()
		)));
	}

	public function testPerformWildcardRoute() {
		$_GET['dachi_uri'] = "/__unit_test/wildcard/";
		$this->assertEquals("got_wildcard_result", Router::performRoute(array(
			"class"     => "Dachi\\Tests\\FakeRouteProvider",
			"method"    => "getWildcardResult",
			"route"     => "__unit_test/:test_variable",
			"variables" => array(array(1, "test_variable"))
		)));
	}

	public function testPerformWildcardChildRoute() {
		$_GET['dachi_uri'] = "/__unit_test/wildcard/test/";
		$this->assertEquals("got_wildcard_child_result", Router::performRoute(array(
			"class"     => "Dachi\\Tests\\FakeRouteProvider",
			"method"    => "getWildcardChildResult",
			"route"     => "__unit_test/:test_variable/test",
			"variables" => array(array(1, "test_variable"))
		)));
	}

	public function testPerformNonWildcardRoute() {
		$_GET['dachi_uri'] = "/__unit_test/test/";
		$this->assertEquals("got_non_wildcard_result", Router::performRoute(array(
			"class"     => "Dachi\\Tests\\FakeRouteProvider",
			"method"    => "getNonWildcardResult",
			"route"     => "__unit_test/test",
			"variables" => array()
		)));
	}

	public function testPerformNonWildcardChildRoute() {
		$_GET['dachi_uri'] = "/__unit_test/test/test/";
		$this->assertEquals("got_non_wildcard_child_result", Router::performRoute(array(
			"class"     => "Dachi\\Tests\\FakeRouteProvider",
			"method"    => "getNonWildcardChildResult",
			"route"     => "__unit_test/test/test",
			"variables" => array()
		)));
	}

	public function testPerformChainedRoute() {
		$_GET['dachi_uri'] = "/__unit_test/chain/result/";
		$this->assertEquals("chain_a", Router::performRoute(array(
			"class"     => "Dachi\\Tests\\FakeRouteProvider",
			"method"    => "getChainedResultB",
			"route"     => "__unit_test/chain/result",
			"variables" => array(),
			"render-path" => "__unit_test/chain"
		)));
	}

	public function testFullRouteToRenderedTemplate() {
		$_GET['dachi_uri'] = "/__unit_test/123/test";

		ob_start();
		Kernel::initialize();
		Router::route();

		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('test <radon-block id="test_block"></radon-block> test', $output);
	}
}

class FakeRouteProvider {
	public function getResult() {
		return "got_result";
	}

	public function getWildcardResult() {
		return "got_wildcard_result";
	}

	public function getWildcardChildResult() {
		return "got_wildcard_child_result";
	}

	public function getNonWildcardResult() {
		return "got_non_wildcard_result";
	}

	public function getNonWildcardChildResult() {
		return "got_non_wildcard_child_result";
	}

	public function getChainedResultA() {
		return "chain_a";
	}

	public function getChainedResultB() {
		return "chain_b";
	}
}