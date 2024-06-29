<?php
namespace Mail\Handler;

use Mail\Message;

interface Handler
{
    public function send(Message $message);
}
