<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class StartMailInput extends ReplyCode
{
    public function __construct(
        public readonly string $text = ''
    )
    {
    }

    public function __toString(): string
    {
        return "354 $this->text\r\n";
    }
}