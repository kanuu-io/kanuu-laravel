<?php

namespace Kanuu\Laravel;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class HandlePaddleWebhook extends Controller
{
    public function __construct()
    {
        $this->middleware(VerifyPaddleWebhook::class);
    }

    public function __invoke(Request $request, Kanuu $kanuu)
    {
        $webhookHandlers = $kanuu->getWebhookHandlersFor(
            $request->get('alert_name')
        );

        if (empty($webhookHandlers)) {
            return response()->json();
        }

        $identifier = $request->has('passthrough')
            ? $kanuu->getModel($this->getIdentifier($request))
            : null;

        foreach ($webhookHandlers as $webhookHandler) {
            $webhookHandler((object) $request->all(), $identifier);
        }

        return response()->json();
    }

    protected function getIdentifier(Request $request): ?string
    {
        $passthrough = json_decode($request->get('passthrough'), true);

        return Arr::get($passthrough, 'kanuu');
    }
}
