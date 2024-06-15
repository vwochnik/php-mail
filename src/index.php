<?php

die($_ENV["SMTP_HOST"]);

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

function sanitize($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
date_default_timezone_set('Etc/UTC');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    fail(400, "only POST supported");
}

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() != JSON_ERROR_NONE) {
    fail(400, "bad request");
}

foreach (array("name", "email", "subject", "message") as $key) {
    if (empty($data[$key])) {
        fail(400, $key ." required");
    }
}

$name = sanitize($data["name"]);
$email = sanitize($data["email"]);
$subject = sanitize($data["subject"]);
$message = sanitize($data["message"]);

if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
    fail(400, "invalid name");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail(400, "invalid email");
}

