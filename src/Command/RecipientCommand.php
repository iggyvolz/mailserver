<?php

namespace iggyvolz\Mailserver\Command;

use iggyvolz\Mailserver\ReplyCode\ParameterSyntaxError;
use iggyvolz\Mailserver\ReplyCode\ReplyCodeException;

final class RecipientCommand extends Command
{
    public function __construct(
        public readonly string $to,
    )
    {
    }


    public static function fromLine(string $line): static
    {
        if(!str_starts_with($line, "TO:<")) {
            throw new ReplyCodeException(new ParameterSyntaxError());
        }
        if(!str_ends_with($line, ">")) {
            throw new ReplyCodeException(new ParameterSyntaxError());
        }
        return new static(substr($line, strlen("TO:<"), -1));
    }
}