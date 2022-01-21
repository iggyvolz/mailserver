<?php

namespace iggyvolz\Mailserver;

use Amp\DeferredCancellation;
use Amp\Socket\SocketServer;
use function Amp\async;
use function Amp\Future\await;

class Mailserver
{
    private array $messages = [];
    public readonly DeferredCancellation $cancellation;

    public function __construct(
        /**
         * @var list<SocketServer>
         */
        public readonly array $servers,
    )
    {
        $this->cancellation = new DeferredCancellation();
    }
    public function run(): void
    {
        await(array_map(fn (SocketServer $server) => async(function(SocketServer $server): void {
            while($socket = $server->accept($this->cancellation->getCancellation())) {
                async(Session::handle(...), $this, $socket, $socket);
            }
        }, $server), $this->servers));
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