<?php

namespace Kanuu\Laravel\Exceptions;

class PaddlePublicKeyMissingException extends KanuuException
{
    protected $message = 'Your Paddle public key is missing. It is required to verify the webhook signatures. Please ensure you added it to your .env file.';
}
