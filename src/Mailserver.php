<?php

namespace iggyvolz\Mailserver;

use Amp\Cancellation;
use Amp\DeferredCancellation;
use Amp\Socket\ResourceSocket;
use Amp\Socket\ResourceSocketServer;
use iggyvolz\Mailserver\Command\Command;
use function Amp\async;
use function Amp\Socket\listen;

class Mailserver
{
    private array $messages = [];
    // TODO encrypt on port 587
    public readonly DeferredCancellation $cancellation;

    public function __construct(
        public readonly string $server = '0.0.0.0',
        public readonly int $port = 25,
    )
    {
        $this->cancellation = new DeferredCancellation();
    }
    public function run(): void
    {
        $server = listen("tcp://$this->server:$this->port");

        while($socket = $server->accept($this->cancellation->getCancellation())) {
            async(Session::handle(...), $this, $socket, $socket);
        }
    }

    public function addMessage(?string $from, array $to, string $contents): void
    {
        $this->messages[] = new Message($from, $to, $contents);
    }

    public function getAllMessages(): array
    {
        return $this->messages;
    }
}