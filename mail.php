<?php
require __DIR__ .'/vendor/autoload.php';

use Dotenv\Dotenv;
use DNSBL\DNSBL;
use Mail\Mail;
use Mail\MailException;
use Stash;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$driver = new Stash\Driver\FileSystem(array(
    "path" => __DIR__ ."/tmp"
));
$pool = new Stash\Pool($driver);

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ ."/templates");
$twig = new \Twig\Environment($loader, []);

$dnsbl = new DNSBL(array(
    'blacklists' => array(
        "dnsbl-1.uceprotect.net",
        "dnsbl-2.uceprotect.net",
        "dnsbl-3.uceprotect.net",
        "dnsbl.dronebl.org",
        "all.s5h.net",
        "b.barracudacentral.org"
    )
));

return new Mail($twig, $dnsbl, $pool);