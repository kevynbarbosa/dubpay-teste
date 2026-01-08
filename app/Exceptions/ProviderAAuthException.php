<?php

namespace App\Exceptions;

class ProviderAAuthException extends \Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct("Failed to authenticate with Provider A: " . $message, $code, $previous);
    }
}
