<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class ServiceReady extends ReplyCode
{
    public function __construct(
        private string $server
    )
    {
    }

    public function __toString(): string
    {
        return "220 $this->server\r\n";
    }
}