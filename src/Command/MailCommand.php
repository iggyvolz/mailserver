<?php

namespace iggyvolz\Mailserver\Command;

use iggyvolz\Mailserver\ReplyCode\ParameterSyntaxError;
use iggyvolz\Mailserver\ReplyCode\ReplyCodeException;

final class MailCommand extends Command
{
    public function __construct(
        public readonly string $from,
    )
    {
    }

    public static function fromLine(string $line): static
    {
        if(!str_starts_with($line, "FROM:<")) {
            throw new ReplyCodeException(new ParameterSyntaxError());
        }
        if(!str_ends_with($line, ">")) {
            throw new ReplyCodeException(new ParameterSyntaxError());
        }
        return new static(substr($line, strlen("FROM:<"), -1));
    }
}