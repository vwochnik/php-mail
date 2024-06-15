<?php
namespace Mail;

use Valitron\Validator;

class Mail
{
    protected $twig;

    public function __construct($twig) {
        $this->twig = $twig;
    }

    public function send($data)
    {
        $v = new Validator($data);
        $v->rule('required', 'name');

        if(!$v->validate())
        {
            //print_r($v->errors());
            throw new MailException("invalid input");
        }

        return $this->twig->render("mail.html", array());
    }

}