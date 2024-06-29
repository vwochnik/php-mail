<?php
namespace Mail;

use Valitron\Validator;
use \PalePurple\RateLimit\RateLimit;
use \PalePurple\RateLimit\Adapter\Stash as StashAdapter;
use Mail\Handler\Handler;
use Mail\Handler\SMTPHandler;


class Mail
{
    protected $dnsbl;
    protected $pool;

    protected Handler $handler;

    protected $adapter;
    protected $rateLimit;

    public function __construct($twig, $dnsbl, $pool)
    {
        $this->dnsbl = $dnsbl;
        $this->pool = $pool;

        $this->adapter = new StashAdapter($this->pool);
        $this->rateLimit = new RateLimit("mail", 3, 3600, $this->adapter);

        $this->handler = new SMTPHandler($twig);
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
            throw new Exception($message);
        }

        if(!$this->rateLimit->check($data["ip"]))
        {
            throw new Exception("rate limit exceeded");
        }

        return new Message($data);
    }

    public function send($message)
    {
        if($this->dnsbl->isListed($message->getIP()))
        {
            throw new Exception("spam detected");
        }

        $this->handler->send($message);
    }

}