<?php

namespace iggyvolz\Mailserver;

class Message
{
    public function __construct(public readonly ?string $from, public readonly array $to, public readonly string $contents)
    {
    }
}