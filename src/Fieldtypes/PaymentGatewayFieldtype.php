<?php

namespace DuncanMcClean\SimpleCommerce\Fieldtypes;

use DuncanMcClean\SimpleCommerce\Facades\PaymentGateway;
use DuncanMcClean\SimpleCommerce\Facades\ShippingMethod;
use Statamic\Fields\Fieldtype;
use Statamic\Fieldtypes\Relationship;

class PaymentGatewayFieldtype extends Fieldtype
{
    protected $selectable = false;

    public function preProcessIndex($data)
    {
        return collect($data)->map(function ($item) {
            $paymentGateway = PaymentGateway::find($item);

            return $paymentGateway->title();
        })->implode(', ');
    }
}
