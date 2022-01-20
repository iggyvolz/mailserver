<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class SyntaxError extends ReplyCode
{
    public function __construct(
    )
    {
        parent::__construct(500);
    }
}