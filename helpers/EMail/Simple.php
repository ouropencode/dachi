<?php
namespace Dachi\Helpers\EMail;

use Dachi\Core\Configuration;
use Dachi\Core\Template;
use Dachi\Core\Kernel;

use PHPMailer;

class Simple extends \Dachi\Helpers\EMail {

	public static function send($options, $extra_options = array()) {
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

		$email = new \PHPMailer();
		$email->isSMTP();
		$email->SMTPAuth = Configuration::get("api.phpmailer.smtp_auth", true);
        $email->SMTPAutoTLS = Configuration::get("api.phpmailer.smtp_auto_tls", true);
		$email->Host = Configuration::get("api.phpmailer.smtp_host");
		$email->Username = Configuration::get("api.phpmailer.smtp_user");
		$email->Password = Configuration::get("api.phpmailer.smtp_pass");
		$email->SMTPSecure = Configuration::get("api.phpmailer.smtp_secure");
		$email->Port = Configuration::get("api.phpmailer.smtp_port");

		$email->Subject = isset($options["subject"]) ? $options["subject"]: $defaultSubject;
		$email->Body = $text;
		$email->setFrom(Configuration::get("api.phpmailer.default_from_email"), Configuration::get("api.phpmailer.default_from_name"));
		$email->addAddress($options["email"]);
		$email->isHTML(true);

		$email->SMTPOptions = array(
			"ssl" => array(
				"allow_self_signed" => Configuration::get("api.phpmailer.allow_self_signed", false),
			)
		);

		$peer_fingerprint = Configuration::get("api.phpmailer.peer_fingerprint", null);
		if($peer_fingerprint != null)
			$email->SMTPOptions["ssl"]["peer_fingerprint"] = $peer_fingerprint;

		$additional = array();
		if(isset($options["attachments"]) && is_array($options["attachments"])) {
			$tempdir = self::tempdir(null, "email_");
			foreach($options["attachments"] as $file) {
				$filename = $tempdir . DIRECTORY_SEPARATOR . $file["name"];
				file_put_contents($filename, $file["content"]);
				if(!isset($additional["attachment"]))
					$additional["attachment"] = array();

				$additional["attachment"][] = $filename;
				$email->AddAttachment($filename, $file["name"]);
			}
		}

		if(!$email->Send())
			throw new PHPMailerException($email->ErrorInfo);

		return true;
	}

	protected static function tempdir($dir = null, $prefix = 'tmp_', $mode = 0700, $maxAttempts = 1000) {
		if (is_null($dir))
			$dir = sys_get_temp_dir();

		$dir = rtrim($dir, '/');

		if (!is_dir($dir) || !is_writable($dir))
			return false;

		if (strpbrk($prefix, '\\/:*?"<>|') !== false)
			return false;

		$attempts = 0;
		do {
			$path = sprintf('%s/%s%s', $dir, $prefix, mt_rand(100000, mt_getrandmax()));
		} while (!mkdir($path, $mode) && $attempts++ < $maxAttempts);

		return $path;
	}

}

class PHPMailerException extends \Exception { }
