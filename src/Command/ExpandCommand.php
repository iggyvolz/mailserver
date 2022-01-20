<?php

namespace iggyvolz\Mailserver\Command;

final class ExpandCommand extends Command
{
    public function __construct(public readonly string $helpString)
    {
    }

    public static function fromLine(string $line): static
    {
        return empty($line) ? new static(null) : new static($line);
    }
}