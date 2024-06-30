<?php
return array(
    "main" => array(
        "mail_addr" => "example@example.com",
        "mail_name" => "Example",
        "handler" => "smtp",
        "dnsbl" => array(
            "dnsbl-1.uceprotect.net",
            "dnsbl-2.uceprotect.net",
            "dnsbl-3.uceprotect.net",
            "dnsbl.dronebl.org",
            "all.s5h.net",
            "b.barracudacentral.org"
        )
    ),
    "smtp" => array(
        "host" => "",
        "port" => 465,
        "username" => "",
        "password" => "",
        "secure" => "ssl"
    )
);
