<?php
require __DIR__ .'/vendor/autoload.php';

use Stash;

$driver = new Stash\Driver\FileSystem(array(
    "path" => __DIR__ ."/tmp"
));
$pool = new Stash\Pool($driver);

$pool->purge();
