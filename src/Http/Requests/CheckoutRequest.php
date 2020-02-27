<?php

namespace DoubleThreeDigital\SimpleCommerce\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
//            'payment_method' => 'required|string',
            'use_shipping_address_for_billing' => 'required|in:on,off',

            'name' => 'required|string',
            'email' => 'required|email',

            'shipping_address_1' => 'required|string',
            'shipping_address_2' => '',
            'shipping_address_3' => '',
            'shipping_city' => 'required|string',
            'shipping_zip_code' => 'required',
            'shipping_country' => 'required|string',
            'shipping_state' => 'nullable|integer',

            'billing_address_1' => 'required_if:use_shipping_address_for_billing,true|string',
            'billing_address_2' => '',
            'billing_address_3' => '',
            'billing_city' => 'required_if:use_shipping_address_for_billing,true|string',
            'billing_zip_code' => 'required_if:use_shipping_address_for_billing,true',
            'billing_country' => 'required_if:use_shipping_address_for_billing,true|string',
            'billing_state' => 'nullable|integer',
        ];
    }
}
