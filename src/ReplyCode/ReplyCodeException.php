<?php

namespace iggyvolz\Mailserver\ReplyCode;

class ReplyCodeException extends \Exception
{
    public function __construct(public readonly ReplyCode $replyCode)
    {
        parent::__construct();
    }
}