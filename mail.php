<?php
https://github.com/archer411/contact-me/blob/master/contact.php:q

function fail($error)
{
    die(json_encode(json_decode('{"status": "error", "error": "' . $error . '"}', true)));
}

function success()
{
    die(json_encode(json_decode('{"status": "success"}', true)));
}

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

//date_default_timezone_set("<your timezone>");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    fail("only POST supported")
}

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() != JSON_ERROR_NONE) {

}

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
require 'vendor/autoload.php';
$mail = new PHPMailer(true);

function died($error)
{
    die(json_encode(json_decode('{"status": "error", "error": "' . $error . '"}', true)));
}

function clean_string($string)
{
    $bad = array(
        "content-type",
        "bcc:",
        "to:",
        "cc:",
        "href"
    );
    return str_replace($bad, "", $string);
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    died("must be a post");
}

if (isset($_POST['email'])) {
    // validate expected data exists
    if (!isset($_POST['name']) || !isset($_POST['subject']) || !isset($_POST['email']) || !isset($_POST['message'])) {
        died("form not valid");
    }

    $name = $_POST['name'];
    $name = strip_tags($name);
    $name = clean_string($name);

    $userEmail = $_POST['email'];
    $userEmail = strip_tags($userEmail);
    $userEmail = clean_string($userEmail);
    $bad       = array(
        "content-type",
        "bcc:",
        "to:",
        "cc:",
        "href"
    );
    $userEmail = filter_var(str_replace($bad, "", $userEmail), FILTER_SANITIZE_EMAIL);

    $userSubject = $_POST["subject"];
    $userSubject = strip_tags($userSubject);
    $userSubject = clean_string($userSubject);

    $message = $_POST['message'];
    $message = strip_tags($message);
    $message = clean_string($message);

    $ipJSON = $_POST['ipJSON'];

    $error_message = "";
    $email_rgx     = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';

    if (!preg_match($email_rgx, $userEmail)) {
        $error_message .= 'email not valid';
    }

    $string_rgx = "/^[A-Za-z .'-]+$/";

    // if (!preg_match($string_rgx, $name)) {
    //     $error_message .= 'Name not valid.';
    // }

    if (strlen($message) < 10) {
        $error_message .= 'message not valid';
    }

    if (strlen($error_message) > 0) {
        //    died($error_message);
        died('server rejected');
    }

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
        $page_output["status"] = "error";
        $page_output["error"] = "phpmailer error";
        $page_output["error_detail"] = $mail->ErrorInfo;
    }
}

echo json_encode($page_output);