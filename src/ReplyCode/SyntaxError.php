<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class SyntaxError extends ReplyCode
{
    public function __toString(): string
    {
        return "500 Syntax Error\r\n";
    }
}