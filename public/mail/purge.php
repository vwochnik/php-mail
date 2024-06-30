<?php
$mail = require dirname(__DIR__, 2) .'/mail.php';

$mail->purge();

die("ok");
