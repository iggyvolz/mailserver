<?php

use iggyvolz\Mailserver\Mailserver;

require_once __DIR__ . "/vendor/autoload.php";
(new Mailserver())->run();