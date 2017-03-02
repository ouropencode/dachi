<?php

namespace Dachi\Helpers;

abstract class EMail extends \Dachi\Core\Helper
{
    public static function initalize()
    {
    }

    public static function send($options)
    {
    }

    protected static function tempdir($dir = null, $prefix = 'tmp_', $mode = 0700, $maxAttempts = 1000)
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

class MailHelperException extends \Exception
{
}
