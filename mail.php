<?php
require __DIR__ .'/vendor/autoload.php';

use Mail\Config;
use Mail\Mail;

$config = new Config(__DIR__);

return new Mail($config);