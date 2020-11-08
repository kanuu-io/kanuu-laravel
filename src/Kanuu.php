<?php

namespace Kanuu\Laravel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;

class Kanuu
{
    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $baseUrl;

    /**
     * Kanuu constructor.
     * @param string $apiKey
     * @param string $baseUrl
     */
    public function __construct(string $apiKey, string $baseUrl)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param mixed $identifier
     * @return array
     */
    public function getNonce($identifier): array
    {
        $url = $this->getUrl('api/nonce');
        $data = ['identifier' => $this->getIdentifier($identifier)];
        $response = Http::withToken($this->apiKey)->post($url, $data);

        return $response->json();
    }

    /**
     * @param mixed $identifier
     * @return RedirectResponse
     */
    public function redirect($identifier): RedirectResponse
    {
        $nonce = $this->getNonce($identifier);

        return redirect($nonce['url']);
    }

    /**
     * @param string $urlSegment
     * @return string
     */
    public function getUrl(string $urlSegment): string
    {
        return sprintf(
            '%s/%s',
            rtrim($this->baseUrl, '/'),
            ltrim($urlSegment, '/'),
        );
    }

    /**
     * @param mixed $identifier
     * @return string
     */
    public function getIdentifier($identifier): string
    {
        if (is_object($identifier) && method_exists($identifier, 'getKanuuIdentifier')) {
            return call_user_func([$identifier, 'getKanuuIdentifier']);
        }

        if ($identifier instanceof Model) {
            return $identifier->getKey();
        }

        return (string) $identifier;
    }
}
