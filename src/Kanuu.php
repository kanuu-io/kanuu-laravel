<?php

namespace Kanuu\Laravel;

use Carbon\CarbonInterface;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route as RouteFacade;
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

    /** @var CarbonInterface|int */
    protected $cacheFor = 3600;

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
     * @param array|null $supplemental
     * @return array
     * @throws KanuuSubscriptionMissingException
     */
    public function getNonce($identifier, ?array $supplemental = null): array
    {
        $url = $this->getUrl('api/nonce');
        $response = Http::withToken($this->apiKey)->post($url, [
            'identifier' => $this->getIdentifier($identifier),
            'supplemental' => $supplemental,
        ]);

        if ($response->status() === 402) {
            throw new KanuuSubscriptionMissingException();
        }

        return $response->json();
    }

    /**
     * @param mixed $identifier
     * @return Subscription
     * @throws KanuuSubscriptionMissingException
     */
    public function getSubscription($identifier): Subscription
    {
        $url = $this->getUrl('api/subscription');
        $data = ['identifier' => $this->getIdentifier($identifier)];
        $response = Http::withToken($this->apiKey)->post($url, $data);

        if ($response->status() === 402) {
            throw new KanuuSubscriptionMissingException();
        }

        return Subscription::fromKanuu($response->json());
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

    /**
     * @param string $url
     * @return Route
     */
    public function redirectRoute(string $url = 'kanuu/{identifier}'): Route
    {
        return RouteFacade::get($url, RedirectToKanuu::class);
    }

    /**
     * @param string $url
     * @return Route
     */
    public function webhookRoute(string $url = 'webhooks/paddle'): Route
    {
        return RouteFacade::post($url, HandlePaddleWebhook::class);
    }
}
