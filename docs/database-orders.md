---
title: Database Orders
---

After a couple thousand orders, your site might start to feel slow when storing orders as entries. However, there is a solution... switching a database.

But wait.. you say. Isn't Statamic a flat-file CMS? The answer is yes but when your site starts to scale, you can run into bottlenecks. Databases have proven to be able to scale well and are easy for querying.

# When should I switch to a database?

-   You have a few hundred/thousand orders and you're starting to see performance suffer
-   You need to do some kind of complicated queries against your orders
-   You want to keep your orders out of version control

# How it works

So, instead of your orders & customers living as entries in your `content` folder, they will live in your database. Simple Commerce uses [Eloquent](https://laravel.com/docs/master/eloquent) in order to talk with the database (Eloquent is a Laravel thing).

# Switching to a database

:::note Note!
The below steps assume you already have a database setup.
:::

I've written a few command to make the process of switching your site to a database as painless as possible.

First, you'll want to run the 'switch' command which will update your Simple Commerce config file & install Runway so you can manage your orders in the [Control Panel](#control-panel-interface).

```
php please sc:switch-to-database
```

The above command will publish some database migrations, you'll need to run the `php artisan migrate` for those migrations to take place.

Then, to move over any existing order & customer entries, you can run the provided 'migrate' command. It may be a good idea to run this per environment (eg. local, staging, production).

```
php please sc:migrate-to-database
```

One thing worth noting is that the above command will not delete the order/customer entries or the collections. That's something you should do yourself after you're satisfied with the migration.

If you receive this error (You must run the `sc:switch-to-database` command before running this command.) when running the `sc:migrate-to-database` command, you should ensure your SC 'content config' looks like this:

```php
// config/simple-commerce.php

'content' => [
    'coupons' => [
        'repository' => \DoubleThreeDigital\SimpleCommerce\Coupons\EntryCouponRepository::class,
        'collection' => 'coupons',
    ],

    'customers' => [
        'repository' => \DoubleThreeDigital\SimpleCommerce\Customers\EloquentCustomerRepository::class,
        'model' => \DoubleThreeDigital\SimpleCommerce\Customers\CustomerModel::class,
    ],

    'orders' => [
        'repository' => \DoubleThreeDigital\SimpleCommerce\Orders\EloquentOrderRepository::class,
        'model' => \DoubleThreeDigital\SimpleCommerce\Orders\OrderModel::class,
    ],

    'products' => [
        'repository' => \DoubleThreeDigital\SimpleCommerce\Products\EntryProductRepository::class,
        'collection' => 'products',
    ],
],
```

If you re-run the command, it should then run as expected.

# Control Panel

When you make the switch, Simple Commerce will install [Runway](https://statamic.com/runway), another addon by me (Duncan McClean). Runway is the thing which lets you manage your database records/Eloquent models in the Control Panel.

Runway has it's own documentation site - you may [read it if you please](https://runway.duncanmcclean.com/control-panel).

# Custom Columns

There are cases where you may wish to add columns to either of the provided tables: `orders`/`customers`. You may do this by simply writing a migration to add a column to the table:

```
php artisan make:migration AddPickupPointColumnToOrdersTable
```

```php
/**
 * Run the migrations.
 *
 * @return void
 */
public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->string('pickup_point')->after('gateway');
    });
}
```

You should then run your migrations:

```
php artisan migrate
```

Once migrated, Simple Commerce will get/set any order or customer data to your custom column, rather than relying on it being saved to the `data` column.

# Customisation

If you need to, there's way to customise/override the Eloquent model used, along with the 'repository'.

## The Model

First, in order to customise the Eloquent model, you'll need to create your own version of the Model in your app, then tell Simple Commerce to use that version instead of the default.

1. Copy the `OrderModel`/`CustomerModel` class from inside Simple Commerce into your site's `App\Models` directory. You will need to also update the 'namespace' of the class.
2. In your `simple-commerce.php` config file, replace the `model` reference with your own:

```php
'model' => \DoubleThreeDigital\SimpleCommerce\Orders\OrderModel::class, // [tl! remove]
'model' => \App\Models\Order::class, // [tl! add]
```

And there you go... that's you using a custom version of the Eloquent model.

## The 'Repository'

1. Create a repository class which extends the default one provided by Simple Commerce
2. In your `simple-commerce.php` config file, with a reference to your new repository:

```
'repository' => \DoubleThreeDigital\SimpleCommerce\Orders\EloquentOrderRepository::class, // [tl! remove]
'repository' => \App\SimpleCommerce\EloquentOrderRepository::class, // [tl! add]
```

And, in theory: that should be you!
