<?php

namespace iggyvolz\Mailserver\Command;

final class QuitCommand extends Command
{

    public static function fromLine(string $line): static
    {
        return new static();
    }
}