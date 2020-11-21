<?php

namespace Kanuu\Laravel\Exceptions;

class KanuuApiKeyMissingException extends KanuuException
{
    protected $message = 'Your Kanuu API key is missing. Please ensure you added it to your .env file.';
}
