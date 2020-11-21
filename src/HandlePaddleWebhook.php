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
        $event = $this->getEvent($request);
        $identifier = $this->getIdentifier($request);
        $model = $kanuu->getModel($identifier);

        foreach ($kanuu->getWebhookHandlersFor($event) as $webhookHandler) {
            $webhookHandler($model, (object) $request->all());
        }

        return response()->json();
    }

    protected function getEvent(Request $request)
    {
        return $request->get('alert_name');
    }

    protected function getIdentifier(Request $request): ?string
    {
        $passthrough = json_decode($request->get('passthrough'), true);

        return Arr::get($passthrough, 'kanuu');
    }
}
