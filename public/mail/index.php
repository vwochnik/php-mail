<?php
$mail = require dirname(__DIR__, 2) .'/mail.php';
use Mail\Exception;

function fail($code, $error)
{
    http_response_code($code);
    header('Content-Type: application/json');
    die(json_encode(array('status' => 'error', 'error' => $error)));
}

function success()
{
    http_response_code(200);
    header('Content-Type: application/json');
    die(json_encode(array('status' => 'success')));
}

function get_ip()
{
    if(!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
date_default_timezone_set('Etc/UTC');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    fail(400, "only POST supported");
}

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() != JSON_ERROR_NONE) {
    fail(400, "bad request");
}

$data["ip"] = get_ip();
$data["agent"] = $_SERVER['HTTP_USER_AGENT'];
$today = new \DateTime('now');
$data["date"] = $today->format('Y-m-d');

try
{
    $message = $mail->validate($data);
}
catch (Exception $e)
{
    fail(400, $e->getMessage());
}

try
{
    $mail->send($message);
    success();
}
catch (MailException $e)
{
    fail(500, $e->getMessage());
}
