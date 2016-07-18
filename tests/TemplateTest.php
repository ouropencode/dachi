<?php
namespace Dachi\Tests;

use Dachi\Core\Kernel;
use Dachi\Core\Template;
use Dachi\Core\Request;

class TemplateTest extends Dachi_TestBase {
	public function testGetTemplate() {
		$template = Template::get("@global/base");
		$this->assertInstanceOf('Twig_Template', $template);
	}

	public function testDisplayTemplate() {
		Template::display("@global/test", "test_block");
		$this->assertEquals(array(
			array("template" => "@global/test", "target_id" => "test_block", "type" => "display_tpl")
		), Template::getRenderQueue());
	}

	public function testRenderTemplateViaNonAjax() {
		ob_start();

		Kernel::initialize();
		Request::setData("test", "our_data");
		Template::display("@global/test", "test_block");
		Template::render();

		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals("test <radon-block id='test_block'>hello world our_data</radon-block> test", $output);
	}

	public function testRenderTemplateWithResponseCodeViaNonAjax() {
		ob_start();

		Kernel::initialize();
		Request::setData("test", "our_data");
		Template::display("@global/test", "test_block");
		Template::render();

		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals("test <radon-block id='test_block'>hello world our_data</radon-block> test", $output);
	}

	public function testRenderTemplateViaAjax() {
		$_GET['radon-ui-ajax'] = "true";

		ob_start();

		Kernel::initialize();
		Request::setData("test", "our_data");
		Template::display("@global/test", "test_block");
		Template::render();

		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('{"data":{"test":"our_data","siteName":"Unnamed Dachi Installation","timezone":"Europe\/London","domain":"localhost","baseURL":"\/","assetsURL":"\/build\/","URI":[]},"response":{"status":"assumed","message":"Assuming successful."},"render_actions":[{"type":"display_tpl","template":"@global\/test","target_id":"test_block"}]}', $output);
	}

	public function testRenderTemplateWithResponseCodeViaAjax() {
		$_GET['radon-ui-ajax'] = "true";

		ob_start();

		Kernel::initialize();
		Request::setData("test", "our_data");
		Request::setResponseCode("success", "success_test");
		Template::display("@global/test", "test_block");
		Template::render();

		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('{"data":{"test":"our_data","siteName":"Unnamed Dachi Installation","timezone":"Europe\/London","domain":"localhost","baseURL":"\/","assetsURL":"\/build\/","URI":[]},"response":{"status":"success","message":"success_test"},"render_actions":[{"type":"display_tpl","template":"@global\/test","target_id":"test_block"}]}', $output);
	}
}
