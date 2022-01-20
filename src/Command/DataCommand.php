<?php

namespace iggyvolz\Mailserver\Command;

final class DataCommand extends Command
{

    public static function fromLine(string $line): static
    {
        return new static();
    }
}