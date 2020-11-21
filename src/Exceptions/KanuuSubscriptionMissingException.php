<?php

namespace Kanuu\Laravel\Exceptions;

class KanuuSubscriptionMissingException extends KanuuException
{
    protected $message = 'Your Kanuu account does not have an active subscription. Log in to https://kanuu.io/login and subscribe to our free plan to get started.';
}
