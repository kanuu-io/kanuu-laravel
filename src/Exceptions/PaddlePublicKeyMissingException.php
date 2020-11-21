<?php

namespace Kanuu\Laravel\Exceptions;

use Exception;

class PaddlePublicKeyMissingException extends Exception
{
    protected $message = 'Your Paddle public key is missing. It is required to verify the webhook signatures. Please ensure you added it to your .env file.';
}
