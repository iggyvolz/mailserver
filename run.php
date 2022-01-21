<?php

use Amp\Socket\BindContext;
use Amp\Socket\Certificate;
use Amp\Socket\ServerTlsContext;
use iggyvolz\Mailserver\MailServer;
use function Amp\Socket\listen;

require_once __DIR__ . "/vendor/autoload.php";

$cert = new Certificate(__DIR__ . '/test/cert.pem', __DIR__ . '/test/key.pem');

$context = (new BindContext())
    ->withTlsContext((new ServerTlsContext())->withDefaultCertificate($cert));
$mailserver = new MailServer([listen("0.0.0.0:2525", $context)]);
try {
    $mailserver->run();
} catch(\Amp\CancelledException) {
}
