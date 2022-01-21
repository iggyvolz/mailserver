<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class Closing extends ReplyCode
{
    public function __construct(
        public readonly ?string $text = null
    )
    {
    }

    public function __toString(): string
    {
        return "221 " . $this->text ?? "";
    }
}