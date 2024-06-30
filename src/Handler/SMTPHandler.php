<?php
namespace Mail\Handler;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Mail\Config;
use Mail\Message;
use Mail\Exception;

class SMTPHandler extends Handler
{
    private Config $config;
    private FilesystemLoader $loader;
    private Environment $twig;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->loader = new FilesystemLoader($config->getTemplateDirectory());
        $this->twig = new Environment($this->loader, []);
    }

    public function send(Message $message)
    {
        $mail = $this->setupMailer();

        try
        {
            //Recipients
            $mail->setFrom($this->config->get("main.mail_addr"), $this->config->get("main.mail_name"));
            $mail->addReplyTo($message->getEmail(), $message->getName());
            $mail->addAddress($this->config->get("main.mail_addr"), $this->config->get("main.mail_name"));

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
        $mail->Host       = $this->config->get("smtp.host");
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->config->get("smtp.username");
        $mail->Password   = $this->config->get("smtp.password");
        $mail->SMTPSecure = $this->config->get("smtp.secure");
        $mail->Port       = $this->config->get("smtp.port");

        return $mail;
    }
}