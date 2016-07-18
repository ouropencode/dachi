<?php
namespace Dachi\Helpers\EMail;

use Dachi\Core\Configuration;
use Dachi\Core\Template;
use Dachi\Core\Kernel;

class Mandrill extends \Dachi\Helpers\EMail {
	protected static $mandrill = null;

	public static function initalize() {
		self::$mandrill = new \Mandrill(Configuration::get('api.mandrill.private'));
	}

	public static function send($options, $mandrill_options = array()) {
		if(self::$mandrill == null)
			self::initalize();

		$assetsURL = str_replace("%v", Kernel::getGitHash(), Configuration::get("dachi.assetsURL", "/build/"));
		$text = Template::get("@global/email")->render(array(
			"name"      => isset($options["name"]) ? $options["name"] : "",
			"lead"      => isset($options["lead"]) ? $options["lead"] : "",
			"content"   => isset($options["content"]) ? $options["content"] : "",

			"contact"   => Configuration::get("contact"),
			"sitename"  => Configuration::get("dachi.siteName"),
			"domain"    => Configuration::get("dachi.domain"),
			"assetsURL" => $assetsURL,
			"logo"      => $assetsURL . "/static/images/logo.png",
			"baseURL"   => Configuration::get("dachi.baseURL")
		));

		$defaultSubject = "Message from " . Configuration::get("dachi.siteName", "unknown");

		$message = array(
			"html"                => $text,
			"subject"             => isset($options["subject"]) ? $options["subject"] : $defaultSubject,
			"from_email"          => Configuration::get("api.mandrill.default_from_email"),
			"from_name"           => Configuration::get("api.mandrill.default_from_name"),
			"important"           => false,
			"auto_html"           => false,
			"inline_css"          => false,
			"preserve_recipients" => false,
			"view_content_link"   => false,
			"track_opens"         => true,
			"track_clicks"        => true,
			"auto_text"           => true,
			"url_strip_qs"        => true,
			"tags"                => array("dachi-v2"),
			"subaccount"          => Configuration::get("api.mandrill.subaccount"),
			"headers"             => array("Reply-To" => Configuration::get("api.mandrill.default_from_email")),
			"to"                  => array(array("email" => $options["email"], "name" => $options["name"], "type" => "to"))
		);

		if(is_array($mandrill_options)) {
			foreach($mandrill_options as $key => $val)
				$message[$key] = $val;
		}

		return self::$mandrill->messages->send($message, false, "Main");
	}

	public static function getMandrill() {
		if($this->mandrill == null)
			self::initalize();

		return $this->mandrill;
	}
}
