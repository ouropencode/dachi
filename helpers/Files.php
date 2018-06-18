<?php
namespace Dachi\Helpers;

use Aws\S3\S3Client;

use Dachi\Core\Configuration;

class Files extends \Dachi\Core\Helper {

	protected static $initalized = false;

	protected static $s3 = null;
	protected static $bucket = null;
	protected static $key_prefix = null;
	protected static $secure_secret = null;
	protected static $secure_bucket = null;
	protected static $secure_key_prefix = null;
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

				self::$bucket            = $credentials["bucket"];
				self::$secure_bucket     = isset($credentials["secure-bucket"]) ? $credentials["secure-bucket"] : self::$bucket;
				self::$key_prefix        = isset($credentials["key-prefix"]) ? $credentials["key-prefix"]  : "uploads/";
				self::$secure_key_prefix = isset($credentials["secure-key-prefix"]) ? $credentials["secure-key-prefix"]  : "uploads/";

				self::$secure_secret = hash('sha256', $credentials["key"]);

				self::$s3 = new S3Client(array(
					"version"     => "latest",
					"region"      => $credentials["region"],
					"credentials" => array("key" => $credentials["key"], "secret" => $credentials["secret"])
				));
				break;
		}
		self::$initalized = true;
	}

	public static function store($filename, $data, $content_type = null, $secure = false) {
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
					"Key"    => self::$key_prefix . $internal_filename,
					"Body"   => $data
				);

				if($content_type)
					$object["ContentType"] = $content_type;

				if($secure == true) {
					$date_string      = (new \DateTime())->format('Ym');
					$generated_prefix = $date_string . hash('sha256', self::$secure_secret . $date_string) . "/";
					$object["Bucket"] = self::$secure_bucket;
					$object["Key"]    = self::$secure_key_prefix . $generated_prefix . $internal_filename;
				}

				$result = self::$s3->putObject($object);
				return $result["ObjectURL"];
		}
	}

	public static function delete($filename, $secure = false) {
		if(!self::$initalized)
			self::initalize();

		switch(self::$handler) {
			case "local":
				if(strpos(realpath(WORKING_DIRECTORY . $filename), realpath(WORKING_DIRECTORY) . DIRECTORY_SEPARATOR) !== 0)
					return false;

				unlink(realpath(WORKING_DIRECTORY . $filename));
				return true;

			case "s3":
				$filename = preg_replace("/^https?:\/\/([^\/]+)\//", "", $filename);

				$object = array(
					"Bucket" => self::$bucket,
					"Key"    => $filename
				);

				if($secure == true)
					$object["Bucket"] = self::$secure_bucket;

				$result = self::$s3->deleteObject($object);
				return true;
		}
	}

	public function getS3() {
		return self::$s3;
	}

}
