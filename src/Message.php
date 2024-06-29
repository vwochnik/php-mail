<?php
namespace Mail;

use Valitron\Validator;

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
				throw new Exception($key ." is invalid");
			}
		}
	}

    public function validate()
    {
        $v = new Validator($this->get());
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
