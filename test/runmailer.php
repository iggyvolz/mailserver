<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Amp\Parallel\Sync\Channel;
use iggyvolz\Mailserver\Mailserver;
return function (Channel $channel): string {
    $mailserver = new Mailserver(port: 2525);
    try {
        $mailserver->run();
    } catch(\Amp\CancelledException) {}
    return serialize($mailserver->getAllMessages());
};