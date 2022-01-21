<?php

use Amp\ByteStream\ReadableStream;
use Amp\ByteStream\WritableStream;
use iggyvolz\Mailserver\Mailserver;
use iggyvolz\Mailserver\Session;
use iggyvolz\Mailserver\test\TestStream;
use Revolt\EventLoop;
use Tester\Assert;
use Tester\Environment;
use function Amp\async;

require_once __DIR__ . "/../vendor/autoload.php";
Environment::setup();
/**
 * @var WritableStream $writer
 * @var ReadableStream $reader
 *
 */
// https://en.wikipedia.org/wiki/Simple_Mail_Transfer_Protocol#SMTP_transport_example
$async = async(function(){
    $mailserver = new Mailserver([]);
    $writer = new TestStream();
    $reader = new TestStream();
    async(fn() => Session::handle($mailserver, $writer, $reader));
    foreach(file(__DIR__ . "/ManualTestContents.txt") as $line) {
        $line = trim($line);
        if(str_starts_with($line, "C:")) {
            $conts = substr($line, strlen("C: "));
            $writer->write("$conts\r\n");
        } elseif(str_starts_with($line, "S:")) {
            $code = substr($line, strlen("S: "), 3);
            $conts = $reader->read();
            Assert::match("#^$code.+\r\n$#m", $conts);
        } else {
            throw new LogicException();
        }
    }
    $messages = $mailserver->getAllMessages();
    Assert::count(1, $messages);
    /**
     * @var \iggyvolz\Mailserver\Message $message
     */
    $message = $messages[0];
    Assert::same("bob@example.org", $message->from);
    Assert::same(["alice@example.com", "theboss@example.com"], $message->to);
    Assert::same(str_replace("\n", "\r\n", <<<EOT
        From: "Bob Example" <bob@example.org>
        To: "Alice Example" <alice@example.com>
        Cc: theboss@example.com
        Date: Tue, 15 Jan 2008 16:02:43 -0500
        Subject: Test message
        
        Hello Alice.
        This is a test message with 5 header fields and 4 lines in the message body.
        Your friend,
        Bob
        
        EOT), $message->contents);
});
EventLoop::run();
$async->await();