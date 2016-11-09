<?php
namespace SampleClient\SampleProject\SampleModule\Controllers;

use Dachi\Core\Controller;
use Dachi\Core\Request;
use Dachi\Core\Database;
use Dachi\Core\Template;

class View extends Controller {

	/**
	 * @route-url /
	 */
	public function root() {
		return $this->list_samples();
	}

	/**
	 * @route-url /ajax-test
	 */
	public function list_samples() {
		$amount = (int) Request::getArgument('amount', '10', '[0-9]+');
		Request::setData("samples", Database::getRepository('SampleModule:Sample')->getRecentSamples($amount));
		Template::display("@SampleModule/index", "page_content");
	}

	/**
	 * @route-url /ajax-test/:id
	 * @route-render /ajax-test
	 */
	public function view_sample() {
		$id = (int) Request::getUri('id', '[0-9]+');
		Request::setData("sample", Database::find("SampleModule:Sample", $id));
		Template::display("@SampleModule/sample-view", "sample_view");
	}

}
