<?php
namespace SampleClient\SampleProject\SampleModule\Controllers;

use Dachi\Core\Controller;
use Dachi\Core\Request;
use Dachi\Core\Database;
use Dachi\Core\Template;

use SampleClient\SampleProject\SampleModule\Models\Sample;

class Create extends Controller {

	/**
	 * @route-url /ajax-test/create
	 * @route-render /ajax-test
	 */
	public function create_sample() {
		$sample = Sample::create();

		$sample->setName(Request::getArgument('name', 'Unnamed', '.*'));
		Database::persist($sample);
		Database::flush();

		Request::setResponseCode("success", "Sample created sucessfully.");
		Template::redirect("/ajax-test");
	}

}
