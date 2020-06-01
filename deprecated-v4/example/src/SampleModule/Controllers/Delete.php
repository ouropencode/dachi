<?php
namespace SampleClient\SampleProject\SampleModule\Controllers;

use Dachi\Core\Controller;
use Dachi\Core\Request;
use Dachi\Core\Database;
use Dachi\Core\Template;

class Delete extends Controller {

	/**
	 * @route-url /ajax-test/:id/delete
	 * @route-render /ajax-test
	 */
	public function delete_sample($data = array()) {
		$id = (int) Request::getUri('id', '[0-9]+');
		$sample = Database::find("SampleModule:Sample", $id);
		Database::remove($sample);
		Database::flush();

		Request::setResponseCode("success", "Sample deleted sucessfully.");
		Template::redirect("/ajax-test");
	}

}
