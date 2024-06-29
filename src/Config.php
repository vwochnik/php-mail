<?php
namespace Mail;

class Config
{
    protected string $root;
    protected array $config;

    public function __construct(string $root)
    {
        $this->root = $root;
        $this->config = \parse_ini_file($this->root . DIRECTORY_SEPARATOR . "config.ini", true);
    }

    public function getTemplateDirectory()
    {
        return $this->root . DIRECTORY_SEPARATOR . "templates";
    }

    public function getStashDirectory()
    {
        return $this->root . DIRECTORY_SEPARATOR . "tmp";
    }

    public function getMainConfiguration()
    {
        return $this->config["main"];
    }

    public function getSMTPConfiguration()
    {
        return $this->config["smtp"];
    }
}
