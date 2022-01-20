<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class ServiceReady extends ReplyCode
{
    public function __construct(
        private string $server
    )
    {
        parent::__construct(220);
    }

    protected function toString(): string
    {
        return $this->server;
    }
}