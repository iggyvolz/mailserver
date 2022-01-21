<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Amp\Parallel\Sync\Channel;
use Amp\Socket\BindContext;
use Amp\Socket\Certificate;
use Amp\Socket\ServerTlsContext;
use iggyvolz\Mailserver\MailServer;
use function Amp\Socket\listen;


return function (Channel $channel): string {
    $cert = new Certificate(__DIR__ . '/cert.pem', __DIR__ . '/key.pem');

    $context = (new BindContext())
        ->withTlsContext((new ServerTlsContext())->withDefaultCertificate($cert));
    $mailserver = new MailServer([listen("0.0.0.0:2525", $context)]);
    try {
        $mailserver->run();
    } catch(\Amp\CancelledException) {
    }
    return serialize($mailserver->getAllMessages());
};