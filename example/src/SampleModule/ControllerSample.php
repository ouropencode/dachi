<?php
namespace SampleClient\SampleProject\SampleModule;

use \Dachi\Core\Controller;
use \Dachi\Core\Request;
use \Dachi\Core\Database;
use \Dachi\Core\Template;

use \Dachi\Helper\Files;

class ControllerSample extends Controller {
	/**
	 * @route-url /ajax-test
	 */
	public function list_samples() {
		$amount = (int) Request::getArgument('amount', '10', '[0-9]+');

		if(!Request::hasData("samples"))
			Request::setData("samples", Database::getRepository('SampleModule:ModelSample')->getRecentSamples($amount));

		Template::display("@SampleModule/index", "page_content");
	}

	/**
	 * @route-url /ajax-test/refresh
	 * @route-render /ajax-test
	 */
	public function refresh_samples() {
		$amount = (int) Request::getArgument('amount', '10', '[0-9]+');
		Request::setData("samples", Database::getRepository('SampleModule:ModelSample')->getRecentSamples($amount));
		Template::display("@SampleModule/sample-list", "samples_list");
	}

	/**
	 * @route-url /ajax-test/:id
	 * @route-render /ajax-test
	 */
	public function view_sample() {
		$id = (int) Request::getUri('id', '[0-9]+');
		Request::setData("sample", Database::find("SampleModule:ModelSample", $id));
		Template::display("@SampleModule/sample-view", "sample_view");
	}

	/**
	 * @route-url /ajax-test/:id/delete
	 * @route-render /ajax-test
	 */
	public function delete_sample($data = array()) {
		$id = (int) Request::getUri('id', '[0-9]+');
		$sample = Database::find("SampleModule:ModelSample", $id);
		Database::remove($sample);
		Database::flush();

		Request::setResponseCode("success", "Sample deleted sucessfully.");

		$amount = (int) Request::getArgument('amount', '10', '[0-9]+');
		Request::setData("samples", Database::getRepository('SampleModule:ModelSample')->getRecentSamples($amount));

		Template::display("@SampleModule/sample-list", "samples_list");
		Template::display("@SampleModule/sample-view-blank", "sample_view");
	}

	/**
	 * @route-url /ajax-test/create
	 * @route-render /ajax-test
	 */
	public function create_sample() {
		$sample = new ModelSample();
		$sample->setName(Request::getArgument('name', 'Unnamed', '.*'));
		$sample->setCreated(new \DateTime());

		Database::persist($sample);
		Database::flush();

		Request::setResponseCode("success", "Sample created sucessfully.");

		$amount = (int) Request::getArgument('amount', '10', '[0-9]+');
		Request::setData("samples", Database::getRepository('SampleModule:ModelSample')->getRecentSamples($amount));
		Request::setData("sample", $sample);

		Template::display("@SampleModule/sample-list", "samples_list");
		Template::display("@SampleModule/sample-view", "sample_view");
	}
}