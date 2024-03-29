<?php

namespace iggyvolz\Mailserver;

use Amp\ByteStream\ReadableStream;
use Amp\ByteStream\WritableBuffer;
use Amp\ByteStream\WritableStream;
use Amp\Socket\EncryptableSocket;
use Amp\Socket\ResourceSocket;
use iggyvolz\Mailserver\Command\Command;
use iggyvolz\Mailserver\Command\DataCommand;
use iggyvolz\Mailserver\Command\ExtendedHelloCommand;
use iggyvolz\Mailserver\Command\HelloCommand;
use iggyvolz\Mailserver\Command\StartTlsCommand;
use iggyvolz\Mailserver\Command\MailCommand;
use iggyvolz\Mailserver\Command\NoopCommand;
use iggyvolz\Mailserver\Command\QuitCommand;
use iggyvolz\Mailserver\Command\ShutdownCommand;
use iggyvolz\Mailserver\Command\RecipientCommand;
use iggyvolz\Mailserver\Command\ResetCommand;
use iggyvolz\Mailserver\ReplyCode\Closing;
use iggyvolz\Mailserver\ReplyCode\CommandNotImplemented;
use iggyvolz\Mailserver\ReplyCode\Okay;
use iggyvolz\Mailserver\ReplyCode\ReplyCode;
use iggyvolz\Mailserver\ReplyCode\ReplyCodeException;
use iggyvolz\Mailserver\ReplyCode\ServiceReady;
use iggyvolz\Mailserver\ReplyCode\StartMailInput;

final class Session
{

    private function __construct(
        private readonly MailServer $mailserver,
        private readonly ReadableStream $reader,
        private readonly WritableStream $writer,
        private readonly bool $enableDebug,
    ) {
        $this->mailData = new WritableBuffer();
    }
    public static function handle(MailServer $mailserver, ReadableStream $read, WritableStream $write, bool $enableDebug): void
    {
        try {
            (new self($mailserver, $read, $write, $enableDebug))->doHandle();
        } catch(\Throwable $t) {
            echo $t::class . ": " . $t->getMessage() . " in " . $t->getFile() . ":" . $t->getLine() . "\n" . $t->getTraceAsString() . "\n";
        } finally {
            // Attempt to close socket
            try {
                $read->close();
            } catch(\Throwable) {}
            try {
                $write->close();
            } catch(\Throwable) {}
        }
    }

    private bool $closing = false;
    private ?string $from = null;
    private array $to = [];
    private ?WritableBuffer $mailData;
    private bool $dataMode = false;

    private function doHandle(): void
    {
        $this->writer->write(new ServiceReady("test.iggyvolz.com"));
        // https://amphp.org/getting-started/tcp-chat/parsing
        $buffer = "";

        while (!$this->closing && null !== $chunk = $this->reader->read()) {
            $buffer .= $chunk;

            while (($pos = strpos($buffer, "\r\n")) !== false) {
                $line = substr($buffer, 0, $pos);
                $buffer = substr($buffer, $pos + 2);
                if($this->dataMode) {
                    if($line === '.') {
                        // Data is over
                        $this->dataMode = false;
                        $this->writer->write(new Okay('localhost'));
                        $this->mailData->close();
                        $this->mailserver->addMessage($this->from, $this->to, $this->mailData->buffer());
                        $this->handleCommand(new ResetCommand());
                        continue;
                    } elseif(($line[0]??'') === '.') {
                        // Remove . prefix
                        $line = substr($line, 1);
                    }
                    $this->mailData->write("$line\r\n");
                } else {
                    try {
                        $replyCode = $this->handleCommand(Command::from($line));
                    } catch(ReplyCodeException $replyCodeException) {
                        $replyCode = $replyCodeException->replyCode;
                    }
                    if(!is_null($replyCode)) {
                        $this->writer->write($replyCode);
                    }
                }
            }
        }
    }

    private function handleCommand(Command $command): ?ReplyCode
    {
        if($command instanceof HelloCommand) {
            return new Okay();
        } elseif($command instanceof ExtendedHelloCommand) {
            $extensions = [];
            if($this->writer instanceof EncryptableSocket) {
                $extensions[] = "STARTTLS";
            }
            return new Okay("localhost", $extensions);
        } elseif($command instanceof MailCommand) {
            // Reset buffers
            $this->from = $command->from;
            $this->to = [];
            $this->mailData = new WritableBuffer();
            return new Okay();
        } elseif($command instanceof RecipientCommand) {
            $this->to[] = $command->to;
            // TODO check that user exists
            return new Okay();
        } elseif($command instanceof DataCommand) {
            $this->dataMode = true;
            return new StartMailInput();
        } elseif($command instanceof ResetCommand) {
            // Reset buffers
            $this->from = null;
            $this->to = [];
            $this->mailData = new WritableBuffer();
            return new Okay();
        } elseif($command instanceof NoopCommand) {
            return new Okay();
        } elseif($command instanceof QuitCommand) {
            $this->closing = true;
            return new Closing();
        } elseif($command instanceof ShutdownCommand && $this->enableDebug) {
            $this->closing = true;
            $this->mailserver->cancellation->cancel();
            return new Closing();
        } elseif($command instanceof StartTlsCommand) {
            if($this->writer instanceof EncryptableSocket) {
                $this->writer->write(new ServiceReady("Ready to start TLS"));
                $this->writer->setupTls();
                return null;
            }
        }
        throw new ReplyCodeException(new CommandNotImplemented($command::class));
    }
}