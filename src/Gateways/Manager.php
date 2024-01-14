<?php

namespace DoubleThreeDigital\SimpleCommerce\Gateways;

use DoubleThreeDigital\SimpleCommerce\Contracts\GatewayManager as Contract;
use DoubleThreeDigital\SimpleCommerce\Contracts\Order as OrderContract;
use DoubleThreeDigital\SimpleCommerce\Exceptions\GatewayCallbackMethodDoesNotExist;
use DoubleThreeDigital\SimpleCommerce\Exceptions\GatewayCheckoutFailed;
use DoubleThreeDigital\SimpleCommerce\Exceptions\GatewayDoesNotExist;
use DoubleThreeDigital\SimpleCommerce\Exceptions\GatewayNotProvided;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\SimpleCommerce;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Manager implements Contract
{
    protected $handle;

    protected $redirectUrl;

    protected $errorRedirectUrl;

    public function use($handle): self
    {
        $this->handle = $handle;

        return $this;
    }

    public function config()
    {
        return $this->resolve()->config();
    }

    public function name()
    {
        return $this->resolve()->name();
    }

    public function isOffsiteGateway(): bool
    {
        return $this->resolve()->isOffsiteGateway();
    }

    public function prepare($request, $order)
    {
        return $this->resolve()->prepare($request, $order);
    }

    public function checkout($request, $order)
    {
        try {
            $checkout = $this->resolve()->checkout($request, $order);
        } catch (GatewayCheckoutFailed $e) {
            throw ValidationException::withMessages([
                'gateway' => $e->getMessage(),
            ]);
        }

        $order = Order::find($order->id());

        $order->gatewayData([
            'use' => $this->handle,
            'data' => $checkout,
        ]);

        $order->save();

        return $checkout;
    }

    public function checkoutRules()
    {
        return $this->resolve()->checkoutRules();
    }

    public function checkoutMessages()
    {
        return $this->resolve()->checkoutMessages();
    }

    public function refund(OrderContract $order): array
    {
        $refund = $this->resolve()->refund($order);

        $order->fresh()->refund($refund);
        $order->save();

        return $refund;
    }

    public function callback(Request $request): bool
    {
        if (method_exists($this->resolve(), 'callback')) {
            return $this->resolve()->callback($request);
        }

        return new GatewayCallbackMethodDoesNotExist("Gateway [{$this->handle}] does not have a `callback` method.");
    }

    public function webhook(Request $request)
    {
        return $this->resolve()->webhook($request);
    }

    public function fieldtypeDisplay($value): array
    {
        return $this->resolve()->fieldtypeDisplay($value);
    }

    public function callbackUrl(array $extraParamters = []): string
    {
        return $this->resolve()->callbackUrl($extraParamters);
    }

    public function withRedirectUrl(string $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    public function withErrorRedirectUrl(string $errorRedirectUrl): self
    {
        $this->errorRedirectUrl = $errorRedirectUrl;

        return $this;
    }

    public function resolve()
    {
        if (! $this->handle) {
            throw new GatewayNotProvided('No gateway provided.');
        }

        $gateway = SimpleCommerce::gateways()->firstWhere('handle', $this->handle);

        if (! $gateway) {
            throw new GatewayDoesNotExist("Gateway [{$this->handle}] does not exist.");
        }

        $data = ['config' => $gateway['config']];

        if ($this->redirectUrl) {
            $data['redirectUrl'] = $this->redirectUrl;
        }

        if ($this->errorRedirectUrl) {
            $data['errorRedirectUrl'] = $this->errorRedirectUrl;
        }

        return resolve($gateway['class'], $data);
    }
}
