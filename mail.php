<?php
require __DIR__ .'/vendor/autoload.php';

use DNSBL\DNSBL;
use Mail\Config;
use Mail\Mail;
use Mail\MailException;

$config = new Config(__DIR__);

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

return new Mail($config, $dnsbl);