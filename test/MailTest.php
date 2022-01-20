<?php

use Amp\Parallel\Context\ProcessContext;
use iggyvolz\Mailserver\Mailserver;
use Tester\Assert;
use Tester\Environment;

require_once __DIR__ . "/../vendor/autoload.php";
try {

    $mailer = ProcessContext::start(__DIR__ . "/runmailer.php");
    Environment::setup();
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->setFrom($from = "from@example.com");
    $to = [];
    $mail->addAddress($to[] = "to@example.com");
    $mail->addAddress($to[] = "to2@example.com");
    $mail->Port = 2525;
    $mail->Subject = 'Subject here';
    $mail->Body = <<<EOF
Hey this is a message body.
Oh shoot I'm about to start a line with a dot.
I hope I escaped it properly
.
..
...
JK the message isn't over
EOF;
    $mail->send();
    // Shutdown server
    $connection = \Amp\Socket\connect("tcp://127.0.0.1:2525");
    $connection->write("SHUT\r\n");
    $connection->read();
    $connection->close();
    $allMessages = unserialize($mailer->join());
    Assert::count(1, $allMessages);
    /**
     * @var \iggyvolz\Mailserver\Message $message
     */
    Assert::type(\iggyvolz\Mailserver\Message::class, $message = $allMessages[0]);
    Assert::same($from, $message->from);
    Assert::same($to, $message->to);
    // Get the actual message from the message
    $contents = substr($message->contents, strpos($message->contents, "\r\n\r\n"));
    Assert::same(trim(str_replace("\n", "\r\n", $mail->Body)), trim($contents));
} finally {
    $mailer?->kill();
}