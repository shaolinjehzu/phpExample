<?php

namespace App\Channels\SMS;


class SMSMessage
{
    public $message;
    public function __construct( $message)
    {
        $this->message = $message;
    }
}
