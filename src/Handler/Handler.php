<?php
namespace Mail\Handler;

use Mail\Message;
use Mail\Exception;

abstract class Handler
{
    abstract public function send(Message $message);

    public static function get($kind, ...$args)
    {
        switch ($kind)
        {
        case "smtp":
            return new SMTPHandler(...$args);
        default:
            throw new Exception("unknown handler: " .$kind);
        }
    }
}
