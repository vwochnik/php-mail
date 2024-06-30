<?php
namespace Mail;

use \PalePurple\RateLimit\RateLimit;
use \PalePurple\RateLimit\Adapter\Stash as StashAdapter;
use Twig\Environment as TwigEnvironment;
use Stash;
use Stash\Pool;
use DNSBL\DNSBL;
use Mail\Handler\Handler;

class Mail
{
    protected Config $config;
    protected DNSBL $dnsbl;
    protected Pool $pool;
    protected RateLimit $rateLimit;
    protected Handler $handler;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->dnsbl = new DNSBL(array(
            'blacklists' => $config->get("main.dnsbl")
        ));

        $this->pool = new Pool(new Stash\Driver\FileSystem(array(
           "path" => $config->get("directories.stash")
       )));

        $this->rateLimit = new RateLimit("mail", 3, 3600, new StashAdapter($this->pool));

        $this->handler = Handler::get($config->get("main.handler"), $config);
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

    public function purge()
    {
        $this->pool->purge();
    }
}