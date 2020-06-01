<?php
namespace Dachi\Helpers;

use Aws\S3\S3Client;

use Dachi\Core\Configuration;

class Files extends \Dachi\Core\Helper {

	protected static $initalized = false;

	protected static $s3 = null;
	protected static $bucket = null;
	protected static $handler = null;

	public static function initalize() {
		self::$handler = Configuration::get("helper.files.handler");
		switch(self::$handler) {
			case "local";
				if(!file_exists("assets/uploads"))
					mkdir("assets/uploads");
				break;

			case "s3":
				$credentials = Configuration::get("api.aws.s3");

				self::$bucket = $credentials["bucket"];

				self::$s3 = new S3Client(array(
					"version"     => "latest",
					"region"      => $credentials["region"],
					"credentials" => array("key" => $credentials["key"], "secret" => $credentials["secret"])
				));
				break;
		}
		self::$initalized = true;
	}

	public static function store($filename, $data, $content_type = null) {
		if(!self::$initalized)
			self::initalize();

		$extension = strrpos($filename, ".") ? substr($filename, strrpos($filename, ".")) : "";
		$internal_filename = hash('sha256', time() . uniqid() . $filename) . $extension;

		switch(self::$handler) {
			case "local":
				file_put_contents("assets/uploads/" . $internal_filename, $data);
				return "/assets/uploads/" . $internal_filename;

			case "s3":
				$object = array(
					"Bucket" => self::$bucket,
					"ACL"    => 'public-read',
					"Key"    => "uploads/" . $internal_filename,
					"Body"   => $data
				);

				if($content_type)
					$object["ContentType"] = $content_type;

				$result = self::$s3->putObject($object);
				return $result["ObjectURL"];
		}
	}

	public static function delete($filename) {
		if(!self::$initalized)
			self::initalize();

		switch(self::$handler) {
			case "local":
				if(strpos(realpath(WORKING_DIRECTORY . $filename), realpath(WORKING_DIRECTORY) . DIRECTORY_SEPARATOR) !== 0)
					return false;

				unlink(realpath(WORKING_DIRECTORY . $filename));
				return true;

			case "s3":
				if(strpos($filename, "/"))
					$filename = substr($filename, strrpos($filename, "/") + 1);

				$result = self::$s3->deleteObject(array(
					"Bucket" => self::$bucket,
					"Key"    => "uploads/" . $filename
				));
				return true;
		}
	}

	public function getS3() {
		return self::$s3;
	}

}
