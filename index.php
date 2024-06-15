<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Mail\Mail;
use Mail\MailException;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ ."/templates");
$twig = new \Twig\Environment($loader, []);

$mail = new Mail($twig);
try
{
    $result = $mail->send(array(
        "name" => "Vincent Wochnik",
        "email" => "v.wochnik@gmail.com",
        "subject" => "Test",
        "message" => "Hello World!"
    ));
    echo $result;
}
catch (MailException $e)
{
    echo $e->getMessage();
}


//require __DIR__ . '/src/index.php';
