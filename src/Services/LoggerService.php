<?php

namespace App\Services;

class LoggerService
{
    public static function Logger($message): void
    {
        $log = 'timestamp => ' . time() . ' IP => ' .$_SERVER['REMOTE_ADDR']. ' message => '. $message . PHP_EOL;
        file_put_contents('public/logs/logs.txt', $log, FILE_APPEND);
    }
}
