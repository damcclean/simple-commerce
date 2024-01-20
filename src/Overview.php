<?php

namespace DoubleThreeDigital\SimpleCommerce;

use Carbon\CarbonPeriod;
use DoubleThreeDigital\SimpleCommerce\Customers\EloquentCustomerRepository;
use DoubleThreeDigital\SimpleCommerce\Customers\EntryCustomerRepository;
use DoubleThreeDigital\SimpleCommerce\Facades\Customer;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use DoubleThreeDigital\SimpleCommerce\Orders\EloquentOrderRepository;
use DoubleThreeDigital\SimpleCommerce\Orders\EntryOrderRepository;
use DoubleThreeDigital\SimpleCommerce\Orders\PaymentStatus;
use DoubleThreeDigital\SimpleCommerce\Orders\StatusLogEvent;
use DoubleThreeDigital\SimpleCommerce\Products\EntryProductRepository;
use DoubleThreeDigital\SimpleCommerce\Support\Runway;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Http\Request;
use Statamic\Facades\Collection;
use Statamic\Facades\Site;
use Statamic\Facades\User;

class Overview
{
    protected static $widgets = [];

    public static function widgets(): array
    {
        return static::$widgets;
    }

    public static function widget(string $handle): ?array
    {
        return collect(static::$widgets)->firstWhere('handle', $handle);
    }

    public static function registerWidget(string $handle, array $config, \Closure $callback)
    {
        static::$widgets[] = array_merge($config, [
            'handle' => $handle,
            'callback' => $callback,
        ]);
    }

    public static function bootCoreWidgets()
    {
        static::registerWidget(
            'orders-chart',
            [
                'name' => __('Orders Chart'),
                'component' => 'overview-orders-chart',
            ],
            function (Request $request) {
                $timePeriod = CarbonPeriod::create(now()->subDays(30)->format('Y-m-d'), now()->format('Y-m-d'));

                return collect($timePeriod)->map(function ($date) {
                    if ((new self)->isOrExtendsClass(SimpleCommerce::orderDriver()['repository'], EntryOrderRepository::class)) {
                        $query = Collection::find(SimpleCommerce::orderDriver()['collection'])
                            ->queryEntries()
                            ->where('payment_status', PaymentStatus::Paid->value)
                            ->whereDate('status_log->paid', $date->format('d-m-Y'))
                            ->get();
                    }

                    if ((new self)->isOrExtendsClass(SimpleCommerce::orderDriver()['repository'], EloquentOrderRepository::class)) {
                        $orderModel = new (SimpleCommerce::orderDriver()['model']);

                        $query = $orderModel::query()
                            ->where('payment_status', PaymentStatus::Paid->value)
                            ->whereDate('data->status_log->paid', $date)
                            ->get();
                    }

                    return [
                        'date' => $date->format('d-m-Y'),
                        'count' => $query->count(),
                    ];
                });
            }
        );

        static::registerWidget(
            'recent-orders',
            [
                'name' => __('Recent Orders'),
                'component' => 'overview-recent-orders',
            ],
            function (Request $request) {
                if ((new self)->isOrExtendsClass(SimpleCommerce::orderDriver()['repository'], EntryOrderRepository::class)) {
                    $query = Collection::find(SimpleCommerce::orderDriver()['collection'])
                        ->queryEntries()
                        ->where('payment_status', PaymentStatus::Paid->value)
                        ->orderBy('status_log->paid', 'desc')
                        ->limit(5)
                        ->get()
                        ->map(function ($entry) {
                            return Order::find($entry->id());
                        })
                        ->values();

                    return $query->map(function ($order) {
                        return [
                            'id' => $order->id(),
                            'order_number' => $order->orderNumber(),
                            'edit_url' => $order->resource()->editUrl(),
                            'grand_total' => Currency::parse($order->grandTotal(), Site::selected()),
                            'paid_at' => $order->statusLog()
                                ->filter(fn (StatusLogEvent $statusLogEvent) => $statusLogEvent->status->is(PaymentStatus::Paid))
                                ->first()
                                ->date()
                                ->format(config('statamic.system.date_format')),
                        ];
                    });
                }

                if ((new self)->isOrExtendsClass(SimpleCommerce::orderDriver()['repository'], EloquentOrderRepository::class)) {
                    $orderModel = new (SimpleCommerce::orderDriver()['model']);

                    $query = $orderModel::query()
                        ->where('payment_status', PaymentStatus::Paid->value)
                        ->orderBy('data->status_log->paid', 'desc')
                        ->limit(5)
                        ->get()
                        ->map(function ($order) {
                            return Order::find($order->id);
                        })
                        ->values();

                    return $query->map(function ($order) use ($orderModel) {
                        return [
                            'id' => $order->id(),
                            'order_number' => $order->orderNumber(),
                            'edit_url' => cp_route('runway.edit', [
                                'resourceHandle' => Runway::orderModel()->handle(),
                                'record' => $order->resource()->{$orderModel->getRouteKeyName()},
                            ]),
                            'grand_total' => Currency::parse($order->grandTotal(), Site::selected()),
                            'paid_at' => $order->statusLog()
                                ->filter(fn (StatusLogEvent $statusLogEvent) => $statusLogEvent->status->is(PaymentStatus::Paid))
                                ->first()
                                ->date()
                                ->format(config('statamic.system.date_format')),
                        ];
                    });
                }

                return null;
            },
        );

        static::registerWidget(
            'top-customers',
            [
                'name' => __('Top Customers'),
                'component' => 'overview-top-customers',
            ],
            function (Request $request) {
                if ((new self)->isOrExtendsClass(SimpleCommerce::customerDriver()['repository'], EntryCustomerRepository::class)) {
                    $query = Collection::find(SimpleCommerce::customerDriver()['collection'])
                        ->queryEntries()
                        ->get()
                        ->sortByDesc(function ($customer) {
                            return count($customer->get('orders', []));
                        })
                        ->take(5)
                        ->map(function ($entry) {
                            return Customer::find($entry->id());
                        })
                        ->values();

                    return $query->map(function ($customer) {
                        return [
                            'id' => $customer->id(),
                            'email' => $customer->email(),
                            'edit_url' => $customer->resource()->editUrl(),
                            'orders_count' => count($customer->get('orders', [])),
                        ];
                    });
                }

                if ((new self)->isOrExtendsClass(SimpleCommerce::customerDriver()['repository'], EloquentCustomerRepository::class)) {
                    $customerModel = new (SimpleCommerce::customerDriver()['model']);

                    $query = $customerModel::query()
                        ->whereHas('orders', function ($query) {
                            $query->where('payment_status', 'paid');
                        })
                        ->withCount('orders')
                        ->orderBy('orders_count', 'desc')
                        ->limit(5)
                        ->get()
                        ->map(function ($customer) {
                            return Customer::find($customer->id);
                        })
                        ->values();

                    return $query->map(function ($customer) use ($customerModel) {
                        return [
                            'id' => $customer->id(),
                            'email' => $customer->email(),
                            'edit_url' => cp_route('runway.edit', [
                                'resourceHandle' => Runway::customerModel()->handle(),
                                'record' => $customer->resource()->{$customerModel->getRouteKeyName()},
                            ]),
                            'orders_count' => $customer->orders()->count(),
                        ];
                    });
                }

                if (config('statamic.users.repository') === 'eloquent') {
                    $userModelClass = config('auth.providers.users.model');
                    $userModel = new $userModelClass;

                    $query = $userModel::query()
                        ->where('orders', '!=', null)
                        ->orderBy(function ($query) {
                            $query->when($query->connection instanceof SQLiteConnection, function ($query) {
                                $query->selectRaw('JSON_ARRAY_LENGTH(orders)');
                            }, function ($query) {
                                $query->selectRaw('JSON_LENGTH(orders)');
                            });
                        }, 'desc')
                        ->limit(5)
                        ->get()
                        ->map(function ($model) {
                            return User::fromUser($model);
                        });
                } else {
                    $query = User::all()
                        ->where('orders', '!=', null)
                        ->sortByDesc(function ($customer) {
                            return count($customer->get('orders', []));
                        })
                        ->take(5)
                        ->map(function ($user) {
                            return Customer::find($user->id());
                        })
                        ->values();
                }

                return $query->map(function ($customer) {
                    return [
                        'id' => $customer->id(),
                        'email' => $customer->email(),
                        'edit_url' => cp_route('users.edit', [
                            'user' => $customer->id(),
                        ]),
                        'orders_count' => count($customer->get('orders', [])),
                    ];
                });
            },
        );

        static::registerWidget(
            'low-stock-products',
            [
                'name' => __('Low Stock Products'),
                'component' => 'overview-low-stock-products',
            ],
            function (Request $request) {
                if ((new self)->isOrExtendsClass(SimpleCommerce::productDriver()['repository'], EntryProductRepository::class)) {
                    $query = Collection::find(SimpleCommerce::productDriver()['collection'])
                        ->queryEntries()
                        ->where('stock', '<', config('simple-commerce.low_stock_threshold'))
                        ->orderBy('stock', 'asc')
                        ->get()
                        ->reject(function ($entry) {
                            return $entry->has('product_variants')
                                || ! $entry->has('stock');
                        })
                        ->take(5)
                        ->map(function ($entry) {
                            return Product::find($entry->id());
                        })
                        ->values();

                    return $query->map(function ($product) {
                        return [
                            'id' => $product->id(),
                            'title' => $product->get('title'),
                            'stock' => $product->stock(),
                            'edit_url' => $product->resource()->editUrl(),
                        ];
                    });
                }

                return null;
            },
        );
    }

    protected function isOrExtendsClass(string $class, string $classToCheckAgainst): bool
    {
        return is_subclass_of($class, $classToCheckAgainst)
            || $class === $classToCheckAgainst;
    }
}
