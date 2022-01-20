<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class StartMailInput extends ReplyCode
{
    public function __construct(
        public readonly ?string $text = null
    )
    {
        parent::__construct(354);
    }

    protected function toString(): string
    {
        return $this->text ?? "";
    }
}