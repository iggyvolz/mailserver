<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class CommandNotImplemented extends ReplyCode
{
    public function __construct(
        public readonly string $method,
    )
    {
        parent::__construct(502);
    }
    protected function toString(): string
    {
        return "Method $this->method not implemented";
    }
}