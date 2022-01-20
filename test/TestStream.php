<?php

namespace iggyvolz\Mailserver\test;

use Amp\ByteStream\ReadableStream;
use Amp\ByteStream\WritableStream;
use function Amp\delay;

class TestStream implements ReadableStream, WritableStream
{
    public function __construct()
    {
    }

    private bool $isClosed = false;
    private ?string $buffer = null;

    public function close(): void
    {
        $this->isClosed = true;
    }

    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    public function read(?\Amp\Cancellation $cancellation = null): ?string
    {
        while($this->buffer === null) {
            delay(0.1);
        }
//        if($this->isClosed && is_null($this->buffer)) {
//            return null;
//        }
        $buffer = $this->buffer;
        $this->buffer = null;
        return $buffer;
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function write(string $bytes): void
    {
        $this->buffer ??= "";
        $this->buffer .= $bytes;
    }

    public function end(): void
    {
        $this->close();
    }

    public function isWritable(): bool
    {
        return true;
    }
}