<?php
namespace Mail;

use Valitron\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \PalePurple\RateLimit\RateLimit;
use \PalePurple\RateLimit\Adapter\Stash as StashAdapter;

class Mail
{
    protected $twig;
    protected $dnsbl;
    protected $pool;

    protected $adapter;
    protected $rateLimit;

    public function __construct($twig, $dnsbl, $pool)
    {
        $this->twig = $twig;
        $this->dnsbl = $dnsbl;
        $this->pool = $pool;

        $this->adapter = new StashAdapter($this->pool);
        $this->rateLimit = new RateLimit("mail", 3, 3600, $this->adapter);
    }

    public function validate($data)
    {
        $v = new Validator($data);
        $v->rule('required', ['name', 'email', 'subject', 'message', 'ip', 'agent']);
        $v->rule('regex', 'name', "/^[\\p{L}'][ \\p{L}'-]*[\\p{L}]$/u");
        $v->rule('email', 'email');
        $v->rule('ip', 'ip');
        $v->rule(function($field, $value, $params, $fields) {
            return ($value == strip_tags($value));
        }, ["name", "subject", "message", "agent"])->message("{field} contains html tags");

        if(!$v->validate())
        {
            $message = implode(", ", array_map(function($k)
            {
                return strtolower($k[0]);
            }, $v->errors()));
            throw new MailException($message);
        }

        if(!$this->rateLimit->check($data["ip"]))
        {
            throw new MailException("rate limit exceeded");
        }

        return new Message($data);
    }

    public function send($message)
    {
        if($this->dnsbl->isListed($message->getIP()))
        {
            throw new MailException("spam detected");
        }


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