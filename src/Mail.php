<?php
namespace Mail;

use Valitron\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    protected $twig;

    public function __construct($twig) {
        $this->twig = $twig;
    }

    public function send($data)
    {
        $v = new Validator($data);
        $v->rule('required', ['name', 'email', 'subject', 'message']);
        $v->rule('regex', 'name', "/^[a-zA-Z-' ]*$/");
        $v->rule('email', 'email');

        if(!$v->validate())
        {
            $message = implode(", ", array_map(function($k)
            {
                return strtolower($k[0]);
            }, $v->errors()));
            throw new MailException($message);
        }

        $data["from"] = $_ENV["MAIL_FROM"];

        $today = new \DateTime('now');
        $data["date"] = $today->format('Y-m-d');

        $mail = $this->setupMailer();

        try
        {
            //Recipients
            $mail->setFrom($data["from"]);
            $mail->addAddress($data["email"], $data["name"]);

            $mail->isHTML(true);
            $mail->Subject = $data["subject"];
            $mail->Body    = $this->twig->render("html.tpl", $data);
            $mail->AltBody = $this->twig->render("text.tpl", $data);

            $mail->send();
        } catch (Exception $e) {
            throw new MailException($e->getMessage());
        }
    }

    private function setupMailer()
    {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = 0;
        $mail->CharSet   = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = $_ENV["SMTP_HOST"];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV["SMTP_USER"];
        $mail->Password   = $_ENV["SMTP_PASS"];
        $mail->SMTPSecure = $_ENV["SMTP_SECURE"];
        $mail->Port       = $_ENV["SMTP_PORT"];

        return $mail;
    }
}