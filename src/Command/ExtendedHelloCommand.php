<?php

namespace iggyvolz\Mailserver\Command;

final class ExtendedHelloCommand extends Command
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