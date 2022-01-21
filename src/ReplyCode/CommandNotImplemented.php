<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class CommandNotImplemented extends ReplyCode
{
    public function __construct(
        public readonly string $method,
    )
    {
    }
    public function __toString(): string
    {
        return "502 Method $this->method not implemented";
    }
}