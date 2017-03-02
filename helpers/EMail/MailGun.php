<?php

namespace Dachi\Helpers\EMail;

use Dachi\Core\Configuration;
use Dachi\Core\Kernel;
use Dachi\Core\Template;
use Mailgun\Mailgun;

class MailGun extends \Dachi\Helpers\EMail
{
    protected static $mailgun = null;

    public static function initalize()
    {
        $client = new \Http\Adapter\Guzzle6\Client();
        self::$mailgun = new self(Configuration::get('api.mailgun.key'), $client);
    }

    public static function send($options, $mailgun_options = [])
    {
        if (self::$mailgun == null) {
            self::initalize();
        }

        if (Configuration::get('api.mailgun.key', null) == null) {
            return false;
        }

        $assetsURL = str_replace('%v', Kernel::getGitHash(), Configuration::get('dachi.assetsURL', '/build/'));
        $text = Template::get('@global/email')->render([
            'name'      => isset($options['name']) ? $options['name'] : '',
            'lead'      => isset($options['lead']) ? $options['lead'] : '',
            'content'   => isset($options['content']) ? $options['content'] : '',

            'contact'   => Configuration::get('contact'),
            'sitename'  => Configuration::get('dachi.siteName'),
            'domain'    => Configuration::get('dachi.domain'),
            'assetsURL' => $assetsURL,
            'logo'      => $assetsURL.'/static/images/logo.png',
            'baseURL'   => Configuration::get('dachi.baseURL'),
        ]);

        $defaultSubject = 'Message from '.Configuration::get('dachi.siteName', 'unknown');

        $default_from_name = Configuration::get('api.mailgun.default_from_name');
        $default_from_email = Configuration::get('api.mailgun.default_from_email');
        $domain = Configuration::get('api.mailgun.domain');

        $message = [
            'from'       => $default_from_name.' <'.$default_from_email.'@'.$domain.'>',
            'to'         => $options['name'].' <'.$options['email'].'>',
            'subject'    => isset($options['subject']) ? $options['subject'] : $defaultSubject,
            'html'       => $text,
            'o:tag'      => ['dachi-v2'],
            'attachment' => [],
        ];

        $additional = [];
        if (isset($options['attachments']) && is_array($options['attachments'])) {
            $tempdir = self::tempdir(null, 'email_');
            foreach ($options['attachments'] as $file) {
                $filename = $tempdir.DIRECTORY_SEPARATOR.$file['name'];
                file_put_contents($filename, $file['content']);
                if (!isset($additional['attachment'])) {
                    $additional['attachment'] = [];
                }

                $additional['attachment'][] = $filename;
            }
        }

        if (is_array($mailgun_options)) {
            foreach ($mailgun_options as $key => $val) {
                $message[$key] = $val;
            }
        }

        return self::$mailgun->sendMessage($domain, $message, $additional);
    }

    public static function getMailgun()
    {
        if ($this->mailgun == null) {
            self::initalize();
        }

        return $this->mailgun;
    }

    private static function tempdir($dir = null, $prefix = 'tmp_', $mode = 0700, $maxAttempts = 1000)
    {
        if (is_null($dir)) {
            $dir = sys_get_temp_dir();
        }

        $dir = rtrim($dir, '/');

        if (!is_dir($dir) || !is_writable($dir)) {
            return false;
        }

        if (strpbrk($prefix, '\\/:*?"<>|') !== false) {
            return false;
        }

        $attempts = 0;
        do {
            $path = sprintf('%s/%s%s', $dir, $prefix, mt_rand(100000, mt_getrandmax()));
        } while (!mkdir($path, $mode) && $attempts++ < $maxAttempts);

        return $path;
    }
}
