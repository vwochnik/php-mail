<?php
namespace Mail\Handler;

use Mail\Config;
use Mail\Message;
use Mail\Exception;

abstract class Handler
{
    abstract public function send(Message $message);

    public static function get(string $kind, Config $config)
    {
        switch ($kind)
        {
        case "smtp":
            return new SMTPHandler($config);
        default:
            throw new Exception("unknown handler: " .$kind);
        }
    }
}
