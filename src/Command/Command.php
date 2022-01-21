<?php

namespace iggyvolz\Mailserver\Command;

use iggyvolz\Mailserver\ReplyCode\CommandNotImplemented;
use iggyvolz\Mailserver\ReplyCode\ReplyCodeException;

abstract class Command
{
    public const COMMANDS = [
        "HELO" => StartTlsCommand::class,
        "EHLO" => ExtendedHelloCommand::class,
        "MAIL" => MailCommand::class,
        "RCPT" => RecipientCommand::class,
        "DATA" => DataCommand::class,
        "RSET" => ResetCommand::class,
        "NOOP" => NoopCommand::class,
        "QUIT" => QuitCommand::class,
        "SHUT" => ShutdownCommand::class,
        "STARTTLS" => StartTlsCommand::class,
    ];

    public abstract static function fromLine(string $line): static;

    public static function from(string $line): self
    {
        $line = explode(" ", $line);
        $cmd = array_shift($line);
        $line = implode(" ", $line);
        if(array_key_exists($cmd, self::COMMANDS)) {
            return self::COMMANDS[$cmd]::fromLine($line);
        } else {
            throw new ReplyCodeException(new CommandNotImplemented($cmd));
        }
    }
}