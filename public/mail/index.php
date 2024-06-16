<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Dotenv\Dotenv;
use DNSBL\DNSBL;
use Mail\Mail;
use Mail\MailException;

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

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
date_default_timezone_set('Etc/UTC');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    fail(400, "only POST supported");
}

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() != JSON_ERROR_NONE) {
    fail(400, "bad request");
}

$data["ip"] = $_SERVER['REMOTE_ADDR'];
$data["agent"] = $_SERVER['HTTP_USER_AGENT'];

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__, 2) ."/templates");
$twig = new \Twig\Environment($loader, []);

$dnsbl = new DNSBL(array(
    'blacklists' => array(
        "dnsbl-1.uceprotect.net",
        "dnsbl-2.uceprotect.net",
        "dnsbl-3.uceprotect.net",
        "dnsbl.dronebl.org",
        "all.s5h.net",
        "b.barracudacentral.org"
    )
));

$mail = new Mail($twig, $dnsbl);

try
{
    $result = $mail->send($data);
    success();
}
catch (MailException $e)
{
    fail(500, $e->getMessage());
}
