<?php

namespace Kanuu\Laravel;

use Closure;
use Illuminate\Http\Request;
use Kanuu\Laravel\Exceptions\PaddlePublicKeyMissingException;

class VerifyPaddleWebhook
{
    public function handle(Request $request, Closure $next)
    {
        if (! $this->hasValidSignature($request)) {
            abort(401, 'Invalid signature');
        }

        return $next($request);
    }

    protected function hasValidSignature(Request $request): bool
    {
        // Ensure the payload has a signature.
        if (! $signature = $request->get('p_signature')) {
            return false;
        }

        // Ensure the Paddle public key is available.
        if (! $publicKey = config('kanuu.providers.paddle.public_key')) {
            throw new PaddlePublicKeyMissingException();
        }

        // Extract the public key and decode the signature.
        $publicKey = openssl_get_publickey($publicKey);
        $signature = base64_decode($signature);

        // Get the rest of the data without the signature.
        $data = $request->except(['p_signature']);

        // Sort the data.
        ksort($data);

        // Prepare the data for serialization.
        foreach ($data as $key => $value) {
            if (! in_array(gettype($value), ['object', 'array'])) {
                $data[$key] = "$value";
            }
        }

        // Serialize the data.
        $data = serialize($data);

        // Verify the signature.
        return filter_var(
            openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA1),
            FILTER_VALIDATE_BOOL
        );
    }
}
