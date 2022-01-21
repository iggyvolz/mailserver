<?php

use Amp\Parallel\Context\ProcessContext;
use iggyvolz\Mailserver\Message;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Tester\Assert;
use Tester\Environment;
use function Amp\Socket\connect;

require_once __DIR__ . "/../vendor/autoload.php";
Environment::lock('port2525', __DIR__);


try {

    $mailer = ProcessContext::start(__DIR__ . "/runmailer.php");
    Environment::setup();
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
//    $mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;
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
    $connection = connect("tcp://127.0.0.1:2525");
    $connection->write("SHUT\r\n");
    $connection->read();
    $connection->close();
    $allMessages = unserialize($mailer->join());
    Assert::count(1, $allMessages);
    /**
     * @var Message $message
     */
    Assert::type(Message::class, $message = $allMessages[0]);
    Assert::same($from, $message->from);
    Assert::same($to, $message->to);
    // Get the actual message from the message
    Assert::same(trim(str_replace("\n", "\r\n", $mail->Body)), trim($message->contents));
} finally {
    $mailer?->kill();
}