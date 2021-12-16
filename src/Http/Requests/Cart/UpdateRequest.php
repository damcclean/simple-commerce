<?php

namespace DoubleThreeDigital\SimpleCommerce\Http\Requests\Cart;

use DoubleThreeDigital\SimpleCommerce\Http\Requests\AcceptsFormRequests;
use DoubleThreeDigital\SimpleCommerce\Orders\Cart\Drivers\CartDriver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateRequest extends FormRequest
{
    use CartDriver;
    use AcceptsFormRequests;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if ($formRequest = $this->get('_request')) {
            return $this->buildFormRequest($formRequest, $this)->rules();
        }

        // v2.4 TODO: Don't validate against blueprints anymore, use an empty array here
        return Arr::except($this->getCart()->rules(), [
            'title',
            'items',
            'slug',
            'paid_date',
            'items_total',
            'coupon_total',
            'tax_total',
            'shipping_total',
            'grand_total',
        ]);
    }

    public function messages()
    {
        if ($formRequest = $this->get('_request')) {
            return $this->buildFormRequest($formRequest, $this)->messages();
        }

        return [];
    }
}
