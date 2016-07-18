<?php
namespace Dachi\Tests;

use Dachi\Core\Request;

class RequestTest extends Dachi_TestBase {

	public function testGetUriAtIntegerIndexWithoutRegex() {
		$_GET['dachi_uri'] = "/part_one/part_two/part_three/part_four/part_five";

		$this->assertEquals("part_one",   Request::getUri(0));
		$this->assertEquals("part_two",   Request::getUri(1));
		$this->assertEquals("part_three", Request::getUri(2));
		$this->assertEquals("part_four",  Request::getUri(3));
		$this->assertEquals("part_five",  Request::getUri(4));
	}

	public function testGetUriAtIntegerIndexWithRegex() {
		$_GET['dachi_uri'] = "/part_one/part_two/part_three/part_four/part_five";

		$this->assertEquals("part_one",   Request::getUri(0, "part_[a-z]+"));
		$this->assertEquals("part_two",   Request::getUri(1, "part_[a-z]+"));
		$this->assertEquals("part_three", Request::getUri(2, "part_[a-z]+"));
		$this->assertEquals("part_four",  Request::getUri(3, "part_[a-z]+"));
		$this->assertEquals("part_five",  Request::getUri(4, "part_[a-z]+"));
	}

	/**
	 * @expectedException Dachi\Core\InvalidRequestURIException
	 */
	public function testGetUriAtIntegerIndexInvalidRegex() {
		$_GET['dachi_uri'] = "/part_one";
		Request::getUri(0, "part_[0-9]+");
	}

	/**
	 * @expectedException Dachi\Core\InvalidRequestURIException
	 */
	public function testGetInvalidUriAtIntegerIndex() {
		$_GET['dachi_uri'] = "/";
		Request::getUri(1);
	}

	public function testGetUriAtStringIndexWithoutRegex() {
		$_GET['dachi_uri'] = "/part_one/part_two/part_three/part_four/part_five";

		Request::setRequestVariables(array(
			array(0, "test_one"),
			array(1, "test_two"),
			array(2, "test_three"),
			array(3, "test_four"),
			array(4, "test_five")
		));


		$this->assertEquals("part_one",   Request::getUri("test_one"));
		$this->assertEquals("part_two",   Request::getUri("test_two"));
		$this->assertEquals("part_three", Request::getUri("test_three"));
		$this->assertEquals("part_four",  Request::getUri("test_four"));
		$this->assertEquals("part_five",  Request::getUri("test_five"));
	}

	public function testGetUriAtStringIndexWithRegex() {
		$_GET['dachi_uri'] = "/part_one/part_two/part_three/part_four/part_five";

		Request::setRequestVariables(array(
			array(0, "test_one"),
			array(1, "test_two"),
			array(2, "test_three"),
			array(3, "test_four"),
			array(4, "test_five")
		));

		$this->assertEquals("part_one",   Request::getUri("test_one",   "part_[a-z]+"));
		$this->assertEquals("part_two",   Request::getUri("test_two",   "part_[a-z]+"));
		$this->assertEquals("part_three", Request::getUri("test_three", "part_[a-z]+"));
		$this->assertEquals("part_four",  Request::getUri("test_four",  "part_[a-z]+"));
		$this->assertEquals("part_five",  Request::getUri("test_five",  "part_[a-z]+"));
	}

	/**
	 * @expectedException Dachi\Core\InvalidRequestURIException
	 */
	public function testGetUriAtStringIndexInvalidRegex() {
		$_GET['dachi_uri'] = "/part_one";

		Request::setRequestVariables(array(
			array(0, "test_one")
		));

		Request::getUri("test_one", "part_[0-9]+");
	}

	/**
	 * @expectedException Dachi\Core\InvalidRequestURIException
	 */
	public function testGetInvalidUriAtStringIndex() {
		$_GET['dachi_uri'] = "/";
		Request::getUri("invalid_part");
	}

	public function testGetFullUri() {
		$_GET['dachi_uri'] = "/part_one/part_two/part_three/part_four/part_five";

		$this->assertEquals(array(
			"part_one", "part_two", "part_three",
			"part_four", "part_five"
		), Request::getFullUri());
	}

	public function testGetAndSetRenderPath() {
		Request::setRenderPath("test_path");
		$this->assertEquals("test_path", Request::getRenderPath());
	}

	public function testGetArgument() {
		$_GET['test_get']     = 'get_test';
		$_POST['test_post']   = 'post_test';
		$_FILES['test_files'] = 'files_test';

		$this->assertEquals("get_test",   Request::getArgument("test_get"));
		$this->assertEquals("post_test",  Request::getArgument("test_post"));
		$this->assertEquals("files_test", Request::getArgument("test_files"));
	}

	public function testGetArgumentDefault() {
		$this->assertEquals("default", Request::getArgument("test"));

		$this->assertEquals("custom_default", Request::getArgument("test",   "custom_default"));
	}

	public function testGetArgumentRegex() {
		$_GET['test_get'] = 'get_test';
		$this->assertEquals("get_test", Request::getArgument("test_get", null, "get_[a-z]+"));
	}

	/**
	 * @expectedException Dachi\Core\InvalidRequestArgumentException
	 */
	public function testGetArgumentInvalidRegex() {
		$_GET['test_get'] = 'get_test';
		$this->assertEquals("get_test", Request::getArgument("test_get", null, "get_[0-9]+"));
	}

	public function testGetSession() {
		$_SESSION['test_sess'] = 'sess_test';
		$this->assertEquals("sess_test", Request::getSession("test_sess"));
	}

	public function testGetSessionDefault() {
		$this->assertEquals("default", Request::getSession("test_sess"));

		$this->assertEquals("custom_default", Request::getSession("test_sess", "custom_default"));
	}

	public function testSetSession() {
		Request::setSession("test_sess", "sess_test");
		$this->assertEquals("sess_test", $_SESSION['test_sess']);
	}

	public function testGetCookie() {
		$_COOKIE['test_cookie'] = "cookie_test";
		$this->assertEquals("cookie_test", Request::getCookie("test_cookie"));
	}

	public function testSetCookie() {
		$success = Request::setCookie("test_cookie", "cookie_test", -1, "/", null);
		$this->assertEquals(true, $success);
	}

	public function testGetAndSetData() {
		Request::setData("test_data", "data_test");
		$this->assertEquals("data_test", Request::getData("test_data"));
	}

	public function testHasData() {
		Request::setData("test_data", "data_test");
		$this->assertEquals(true, Request::hasData("test_data"));
	}

	public function testGetAllData() {
		Request::setData("test_dataA", "data_testA");
		Request::setData("test_dataB", "data_testB");
		Request::setData("test_dataC", "data_testC");

		$this->assertEquals(array(
			"test_dataA" => "data_testA",
			"test_dataB" => "data_testB",
			"test_dataC" => "data_testC"
		), Request::getAllData());
	}

	public function testDefaultResponseCode() {
		$this->assertEquals(array(
			"status" => "assumed",
			"message" => "Assuming successful."
		), Request::getResponseCode());
	}

	public function testGetAndSetResponseCode() {
		Request::setResponseCode("success", "test success");
		$this->assertEquals(array(
			"status" => "success",
			"message" => "test success"
		), Request::getResponseCode());

		Request::setResponseCode("error", "test error");
		$this->assertEquals(array(
			"status" => "error",
			"message" => "test error"
		), Request::getResponseCode());
	}

	public function testIsAjaxViaNonAjax() {
		$this->assertEquals(false, Request::isAjax());
	}

	public function testIsAjaxViaAjax() {
		$_GET['radon-ui-ajax'] = "true";
		$this->assertEquals(true, Request::isAjax());
	}
}
