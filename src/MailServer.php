<?php

namespace iggyvolz\Mailserver;

use Amp\DeferredCancellation;
use Amp\Socket\SocketServer;
use function Amp\async;
use function Amp\Future\await;

class MailServer
{
    private array $messages = [];
    public readonly DeferredCancellation $cancellation;

    public function __construct(
        /**
         * @var list<SocketServer>
         */
        public readonly array $servers,
        public readonly bool $enableDebug = false,
    )
    {
        $this->cancellation = new DeferredCancellation();
    }
    public function run(): void
    {
        await(array_map(fn (SocketServer $server) => async(function(SocketServer $server): void {
            while($socket = $server->accept($this->cancellation->getCancellation())) {
                async(Session::handle(...), $this, $socket, $socket, $this->enableDebug);
            }
        }, $server), $this->servers));
    }

    public function addMessage(?string $from, array $to, string $contents): void
    {
        $headers = [];
        $lines = explode("\r\n", $contents);
        while(($line = array_shift($lines)) !== "") {
            [$key, $value] = explode(":", $line);
            $headers[trim($key)] = trim($value);
        }
        $this->messages[] = new Message($from, $to, $headers, implode("\r\n", $lines));
    }

    public function getAllMessages(): array
    {
        return $this->messages;
    }
}