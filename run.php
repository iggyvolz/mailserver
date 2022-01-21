<?php

use iggyvolz\Mailserver\Mailserver;

require_once __DIR__ . "/vendor/autoload.php";
(new Mailserver([\Amp\Socket\listen("0.0.0.0:2525")]))->run();