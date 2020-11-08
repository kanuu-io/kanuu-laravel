<?php

namespace Kanuu\Laravel\Exceptions;

use Exception;

class KanuuApiKeyMissingException extends Exception
{
    protected $message = 'Your Kanuu API key is missing. Please ensure you added it to your .env file.';
}
