<?php
namespace Mail;

use \PalePurple\RateLimit\RateLimit;
use \PalePurple\RateLimit\Adapter\Stash as StashAdapter;
use Twig\Environment as TwigEnvironment;
use Stash\Pool;
use DNSBL\DNSBL;
use Mail\Handler\Handler;

class Mail
{
    protected DNSBL $dnsbl;
    protected Pool $pool;

    protected Handler $handler;

    protected $adapter;
    protected $rateLimit;

    public function __construct(TwigEnvironment $twig, DNSBL $dnsbl, Pool $pool)
    {
        $this->dnsbl = $dnsbl;
        $this->pool = $pool;

        $this->adapter = new StashAdapter($pool);
        $this->rateLimit = new RateLimit("mail", 3, 3600, $this->adapter);

        $this->handler = Handler::get($_ENV["HANDLER"], $twig);
    }

    public function makeMessage(array $data)
    {
        $message = new Message($data);
        $message->validate();
        return $message;
    }

    public function send($message)
    {
        if(!$this->rateLimit->check($message->getIP()))
        {
            throw new Exception("rate limit exceeded");
        }

        if($this->dnsbl->isListed($message->getIP()))
        {
            throw new Exception("spam detected");
        }

        $this->handler->send($message);
    }
}