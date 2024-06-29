<?php
namespace Mail\Handler;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;
use Twig\Environment as TwigEnvironment;
use Mail\Message;
use Mail\Exception;

class SMTPHandler extends Handler
{
    private TwigEnvironment $twig;

    public function __construct(TwigEnvironment $twig)
    {
        $this->twig = $twig;
    }

    public function send(Message $message)
    {
        $mail = $this->setupMailer();

        try
        {
            //Recipients
            $mail->setFrom($_ENV["MAIL_ADDR"], $_ENV["MAIL_NAME"]);
            $mail->addReplyTo($message->getEmail(), $message->getName());
            $mail->addAddress($_ENV["MAIL_ADDR"], $_ENV["MAIL_NAME"]);

            $mail->isHTML(true);
            $mail->Subject = $message->getSubject();
            $mail->Body    = $this->twig->render("html.tpl", $message->get());
            $mail->AltBody = $this->twig->render("text.tpl", $message->get());

            $mail->send();
        } catch (MailException $e) {
            throw new Exception($e->getMessage());
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