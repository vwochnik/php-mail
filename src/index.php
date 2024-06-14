<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

$mail = new PHPMailer(true);

$email_body = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><style>body{margin:2rem}pre{background-color:#cacaca;width:420px;padding:10px}p{margin-bottom:1rem}summary{border:2px solid #001fff;background:#4057ff;color:white;display:inline;padding:.25rem 1rem;user-select:none}summary:hover{cursor:pointer}summary::-webkit-details-marker{display:none}ul li{margin:1rem 0}details[open] summary{background:#fff;color:black}ul{background:hsla(300,20%,90%,.8)}details ul{padding:1rem}</style></head></body>';
$email_body .= "<b>Date:</b> " . date("m/d/Y") . "<br>";
$email_body .= "<b>Time:</b> " . date("h:i a") . "<br><br>";
$email_body .= "<b>Name:</b> " . $name . "<br>";
$email_body .= "<b>Email:</b> " . $userEmail . "<br>";
$email_body .= "<b>Subject:</b> " . $userSubject . "<br>";
$email_body .= "<b>Message:</b><br> " . $message . "<br>";
$email_body .= "<br><br><br><navigation><details><summary>IP Data</summary><pre>" . json_encode($ipJSON, JSON_PRETTY_PRINT) . "</pre></details></navigation>";
$email_body .= "</body></html>";

try {
    //Server settings
    $mail->SMTPDebug = 0;
    $mail->CharSet   = 'UTF-8';
    $mail->isSMTP();
    $mail->Host       = 'smtp.example.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'send@example.com';
    $mail->Password   = 'password123';
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    //Recipients
    $mail->setFrom('send@example.com', "Mail Bot");
    $mail->addAddress('you@example.com', "Bob Joe");

    //Content
    $mail->isHTML(true);
    $mail->Subject = '❗ Contact Me Form ❗';
    $mail->Body    = $email_body;

    $mail->send();
    $page_output["status"] = "message sent";
} catch (Exception $e) {
    fail(500, "can't send email")
}