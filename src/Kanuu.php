<?php

namespace Kanuu\Laravel;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Kanuu\Laravel\Exceptions\KanuuSubscriptionMissingException;

class Kanuu
{
    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $baseUrl;

    /** @var Closure|null */
    protected $modelResolver;

    /** @var array */
    protected $webhookHandlers = [];

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
     * @throws KanuuSubscriptionMissingException
     */
    public function getNonce($identifier): array
    {
        $url = $this->getUrl('api/nonce');
        $data = ['identifier' => $this->getIdentifier($identifier)];
        $response = Http::withToken($this->apiKey)->post($url, $data);

        if ($response->status() === 402) {
            throw new KanuuSubscriptionMissingException();
        }

        return $response->json();
    }

    /**
     * @param mixed $identifier
     * @return RedirectResponse
     * @throws KanuuSubscriptionMissingException
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

    /**
     * @param Closure $modelResolver
     * @return static
     */
    public function getModelUsing(Closure $modelResolver): Kanuu
    {
        $this->modelResolver = $modelResolver;

        return $this;
    }

    /**
     * @param string|null $identifier
     * @return mixed
     */
    public function getModel(?string $identifier)
    {
        if (! $resolver = $this->modelResolver) {
            return $identifier;
        }

        return $resolver($identifier);
    }

    /**
     * @param string $event
     * @param Closure $webhookHandler
     * @return static
     */
    public function on(string $event, Closure $webhookHandler): Kanuu
    {
        $this->webhookHandlers[$event] = $webhookHandler;

        return $this;
    }

    /**
     * @param string $event
     * @return array
     */
    public function getWebhookHandlersFor(string $event): array
    {
        return array_filter($this->webhookHandlers, function ($eventPattern) use ($event) {
            return Str::is($eventPattern, $event);
        }, ARRAY_FILTER_USE_KEY);
    }
}
