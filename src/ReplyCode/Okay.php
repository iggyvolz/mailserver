<?php

namespace iggyvolz\Mailserver\ReplyCode;

final class Okay extends ReplyCode
{
    public function __construct(
        public readonly string $text = '',
        public readonly array $extensions = [],
    )
    {
    }

    public function __toString(): string
    {

        $lines = [$this->text, ...$this->extensions];
        $lines = array_map(fn(string $s): string => "250-$s", $lines);
        // Last line should start with "250 " not "250-"
        $lines[array_key_last($lines)][3] = " ";
        return implode("\r\n", $lines) . "\r\n";
    }
}