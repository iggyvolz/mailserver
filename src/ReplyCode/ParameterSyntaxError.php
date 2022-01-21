<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class ParameterSyntaxError extends ReplyCode
{
    public function __toString(): string
    {
        return "501 Parameter Syntax Error\r\n";
    }
}