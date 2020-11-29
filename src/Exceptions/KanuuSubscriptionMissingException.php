<?php

namespace Kanuu\Laravel\Exceptions;

class KanuuSubscriptionMissingException extends KanuuException
{
    protected $message = 'Your Kanuu account requires an active subscription. Log in to https://kanuu.io/login and subscribe to our "Unlimited" plan to continue.';
}
