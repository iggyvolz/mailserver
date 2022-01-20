<?php

namespace iggyvolz\Mailserver\ReplyCode;

use Exception;

abstract class ReplyCode extends Exception implements \Stringable
{
    public function __construct(
        public readonly int $number,
    )
    {
        parent::__construct($this->__toString(), $this->number);
    }
    protected function toString(): string
    {
        return "";
    }
    public function __toString(): string
    {
        return "$this->number " . $this->toString() . "\r\n";
    }
}