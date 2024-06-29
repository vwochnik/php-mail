<?php
namespace Mail;

class Message
{
    protected $name;
    protected $email;
    protected $subject;
    protected $message;
    protected $agent;
    protected $ip;
    protected $date;

	public function __construct(array $data)
	{
		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this->{$key} = $value;
			} else {
				throw new MailException('Property "' . $key . '" doesn\'t exists in ' . get_class($this));
			}
		}
	}

    public function get()
    {
        return array(
            "name" => $this->name,
            "email" => $this->email,
            "subject" => $this->subject,
            "message" => $this->message,
            "agent" => $this->agent,
            "ip" => $this->ip,
            "date" => $this->date
        );
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getAgent()
    {
        return $this->agent;
    }

    public function getIP()
    {
        return $this->ip;
    }

    public function getDate()
    {
        return $this->date;
    }
}
