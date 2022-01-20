<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class Okay extends ReplyCode
{
    public function __construct(
        public readonly ?string $text = null
    )
    {
        parent::__construct(250);
    }

    protected function toString(): string
    {
        return $this->text ?? "";
    }
}