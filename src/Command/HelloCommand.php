<?php

namespace iggyvolz\Mailserver\Command;

final class HelloCommand extends Command
{
    public function __construct(
        public readonly string $domain
    )
    {
    }

    public static function fromLine(string $line): static
    {
        return new static($line);
    }
}