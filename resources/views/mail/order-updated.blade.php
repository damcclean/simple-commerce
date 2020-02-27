@component('mail::message')
    # Order updated

    Hi {{ $customer['name'] }},

    We're emailing you about the purchase you recently made from {{ config('app.name') }}. This email is to let you know that the status of your order has been updated. It is now {{ $order['status'] }}.

    @component('mail::panel')
        **Shipping Address**

        {{ $customer['address'] }}
        {{ $customer['country'] }}
        {{ $customer['zip_code'] }}
    @endcomponent

    If you have any questions about your order, simply reach out to us.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
