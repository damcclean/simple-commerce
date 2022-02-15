<?php

namespace DoubleThreeDigital\SimpleCommerce\Customers;

use DoubleThreeDigital\SimpleCommerce\Contracts\Customer as Contract;
use DoubleThreeDigital\SimpleCommerce\Exceptions\CustomerNotFound;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\SimpleCommerce;
use DoubleThreeDigital\SimpleCommerce\Support\Traits\HasData;
use DoubleThreeDigital\SimpleCommerce\Support\Traits\IsEntry;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;

class Customer implements Contract
{
    use IsEntry;
    use HasData;
    use Notifiable;

    public $id;
    public $site;
    public $title;
    public $slug;
    public $data;
    public $published;

    protected $entry;
    protected $collection;

    public function findByEmail(string $email): self
    {
        $entry = Entry::query()
            ->where('collection', $this->collection())
            ->whereIn('slug', [$email, Str::slug($email)])
            ->first();

        if (! $entry) {
            throw new CustomerNotFound(__('simple-commerce::messages.customer_not_found_by_email', [
                'email' => $email,
            ]));
        }

        return $this->find($entry->id());
    }

    public function name(): string
    {
        return $this->get('name');
    }

    public function email(): string
    {
        return $this->get('email');
    }

    public function generateTitleAndSlug(): self
    {
        $name = '';
        $email = '';

        if ($this->has('name')) {
            $name = $this->get('name');
        }

        if ($this->has('email')) {
            $email = $this->get('email');
        }

        $title = __('simple-commerce::messages.customer_title', [
            'name'  => $name,
            'email' => $email,
        ]);

        $this->title = $title;
        $this->data['title'] = $title;

        $this->slug = $email;

        return $this;
    }

    public function orders(): Collection
    {
        return collect($this->has('orders') ? $this->get('orders') : [])
            ->map(function ($orderId) {
                return Order::find($orderId);
            });
    }

    public function addOrder($orderId): self
    {
        $orders = $this->has('orders') ? $this->get('orders') : [];
        $orders[] = $orderId;

        $this->set('orders', $orders);

        return $this;
    }

    public function routeNotificationForMail($notification = null)
    {
        return $this->email();
    }

    public function collection(): string
    {
        return SimpleCommerce::customerDriver()['collection'];
    }

    public static function bindings(): array
    {
        return [];
    }
}
