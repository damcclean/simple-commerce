<?php

namespace DoubleThreeDigital\SimpleCommerce\Http\Controllers;

use DoubleThreeDigital\SimpleCommerce\Events\ReceiveGatewayWebhook;
use DoubleThreeDigital\SimpleCommerce\Exceptions\GatewayDoesNotExist;
use DoubleThreeDigital\SimpleCommerce\Facades\Gateway;
use DoubleThreeDigital\SimpleCommerce\SimpleCommerce;
use Illuminate\Http\Request;

class GatewayWebhookController extends BaseActionController
{
    public function index(Request $request, $gateway)
    {
        $gateway = collect(SimpleCommerce::gateways())
            ->where('handle', $gateway)
            ->first();

        if (! $gateway) {
            throw new GatewayDoesNotExist(__('simple-commerce::messages.gateway_does_not_exist', [
                'gateway' => $gateway,
            ]));
        }

        event(new ReceiveGatewayWebhook($request->all()));

        return Gateway::use($gateway['class'])->webhook($request);
    }
}
