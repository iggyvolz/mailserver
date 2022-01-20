<?php

namespace iggyvolz\Mailserver\Command;

use iggyvolz\Mailserver\ReplyCode\CommandNotImplemented;

abstract class Command
{
    public const COMMANDS = [
        "HELO" => HelloCommand::class,
        "MAIL" => MailCommand::class,
        "RCPT" => RecipientCommand::class,
        "DATA" => DataCommand::class,
        "RSET" => ResetCommand::class,
        "NOOP" => NoopCommand::class,
        "QUIT" => QuitCommand::class,
        "SHUT" => ShutdownCommand::class,
    ];

    public abstract static function fromLine(string $line): static;

    public static function from(string $line): self
    {
        $cmd = strtoupper(substr($line, 0, 4));
        $line = substr($line, 5);
        if(array_key_exists($cmd, self::COMMANDS)) {
            return self::COMMANDS[$cmd]::fromLine($line);
        } else {
            throw new CommandNotImplemented($cmd);
        }
    }
}