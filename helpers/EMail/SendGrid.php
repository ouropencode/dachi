<?php

namespace Dachi\Helpers\EMail;

use Dachi\Core\Configuration;
use Dachi\Core\Kernel;
use Dachi\Core\Template;

class SendGrid extends \Dachi\Helpers\EMail
{
    protected static $sendgrid = null;

    public static function initalize()
    {
        self::$sendgrid = new \SendGrid(Configuration::get('api.sendgrid.key'));
    }

    public static function send($options)
    {
        if (self::$sendgrid == null) {
            self::initalize();
        }

        if (Configuration::get('api.sendgrid.key', null) == null) {
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

        $default_from_name = Configuration::get('api.sendgrid.default_from_name');
        $default_from_email = Configuration::get('api.sendgrid.default_from_email');
        $domain = Configuration::get('api.sendgrid.domain');

        $from = new \SendGrid\Email($default_from_name, $default_from_email.'@'.$domain);
        $to = new \SendGrid\Email($options['name'], $options['email']);
        $subject = substr(isset($options['subject']) ? $options['subject'] : $defaultSubject, 0, 78);
        $content = new \SendGrid\Content('text/html', $text);
        $mail = new \SendGrid\Mail($from, $subject, $to, $content);
        $mail->addCustomArg('sender', 'dachi-v2');

        if (isset($options['attachments']) && is_array($options['attachments'])) {
            $tempdir = self::tempdir(null, 'email_');
            foreach ($options['attachments'] as $file) {
                $attachment = new \SendGrid\Attachment();
                $attachment->setContent(base64_encode($file['content']));
                $attachment->setFilename($file['name']);
                $attachment->setDisposition('attachment');
                $attachment->setContentId(md5($file['name']));
                $mail->addAttachment($attachment);
            }
        }

        $response = self::$sendgrid->client->mail()->send()->post($mail);

        return $response;
    }

    public static function getSendGrid()
    {
        if ($this->sendgrid == null) {
            self::initalize();
        }

        return $this->sendgrid;
    }
}
