<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class ParameterSyntaxError extends ReplyCode
{
    public function __construct(
    )
    {
        parent::__construct(501);
    }
}