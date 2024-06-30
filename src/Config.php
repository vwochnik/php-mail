<?php
namespace Mail;

class Config
{
    protected string $root;
    protected array $config;

    public function __construct(string $root)
    {
        $this->root = $root;
        $this->config = require $this->root . DIRECTORY_SEPARATOR . "config.php";
    }

    public function getTemplateDirectory()
    {
        return $this->root . DIRECTORY_SEPARATOR . "templates";
    }

    public function getStashDirectory()
    {
        return $this->root . DIRECTORY_SEPARATOR . "tmp";
    }

    function get($path, $default = null)
    {
        $current = $this->config;
        $p = strtok($path, '.');

        while ($p !== false) {
            if (!isset($current[$p])) {
                return $default;
            }
            $current = $current[$p];
            $p = strtok('.');
        }

        return $current;
    }
}
