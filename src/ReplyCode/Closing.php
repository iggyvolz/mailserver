<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class Closing extends ReplyCode
{
    public function __construct(
        public readonly ?string $text = null
    )
    {
        parent::__construct(221);
    }

    protected function toString(): string
    {
        return $this->text ?? "";
    }
}