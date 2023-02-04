# Changelog

## Unreleased

## v4.5.2 (2023-02-01)

### What's fixed

-   Product stock is now checked when updating existing line items #797 #801 by @duncanmcclean

## v4.5.1 (2023-01-28)

### What's new

-   Fix Money fieldtype behaving badly #791 #795 by @duncanmcclean

## v4.5.0 (2023-01-28)

### What's new

-   Statamic 3.4 Support #793

## v4.4.2 (2023-01-19)

### What's new

-   Added support for separate first & last name fields for order addresses #788

## v4.4.1 (2023-01-14)

### What's fixed

-   Fixed issue where orders field was being added to every blueprint #785 #786

## v4.4.0 (2023-01-07)

### What's new

-   Danish translation #776 by @rabol

### What's improved

-   All strings in the Control Panel & front-end validation from Simple Commerce are now translatable #777
-   Some of the form validation code has been refactored #780
-   The order of parameters being passed into the `StockRunningLow` and `StockRunOut` events has been changed. #782

## v4.3.1 (2023-01-04)

### What's fixed

-   Fixed `{{ sc:gateways }}` tag when specifying a gateway #774 #775

## v4.3.0 (2023-01-03)

### What's new

-   Redesigned [documentation site](https://simple-commerce.duncanmcclean.com)!
-   PHP 8.2 is now officially supported!
-   An 'Orders' field will be added to Customer/User blueprints automatically, if not already present #772

### What's improved

-   Made some tweaks to the `CheckoutController` - no new features #773

## v4.2.4 (2022-12-16)

### What's new

-   Allow for translating order emails #768 #769

### What's fixed

-   Fixed filtering by country on `{{ sc:regions }}` tag #767 by @el-schneider

## v4.2.3 (2022-11-12)

### What's new

-   A new `{{ sc:cart:tax_total_split }}` tag which returns a cart's tax total, split by tax rates #759 by @Sm1lEE

## v4.2.2 (2022-11-02)

### What's improved

-   You can now pass external URLs as the `redirect` parameter on Form Tags #756 #757

### What's fixed

-   Fixed offsite payments with the PayPal gateway #755 #758

## v4.2.1 (2022-10-29)

### What's fixed

-   Fixed stock not being checked at all when adding products to the cart #752 by @marcvdm

## v4.2.0 (2022-10-22)

Note: v4.2 now requires a minimum of Statamic v3.3.48.

### What's new

-   Added `raw_price` computed value for products #749

## v4.1.3 (2022-10-20)

### What's fixed

-   Fixed 'Order Shipped' email #743 #744
-   Fixed coupons not working when using Database Customer Driver #742 #745 by @jornwildenbeest

## v4.1.2 (2022-10-19)

### What's fixed

-   Fixed a bug where marking an order as shipped could override order data #741 by @cesaramirez

## v4.1.1 (2022-10-17)

### What's fixed

-   Fixed `{{ sc:customer:update }}` tag error #739 by @cesaramirez

## v4.1.0 (2022-10-08)

### What's new

-   You can now filter orders by customer #737
-   You can now filter orders by products #736

### What's improved

-   Deleted products on orders are now handled better now #732 #734

### What's fixed

-   Renamed filenames of blueprint stubs to match the starter kit
-   SC will now check if the 'Stock' field exists on the product's blueprint before trying to check if it's localisable #721 #733

## v4.0.3 (2022-10-01)

### What's new

-   Information about your Simple Commerce configuration will now be outputted by `php please support:details` and `php artisan about` #729

### What's fixed

-   Product Variant not being displayed when viewing Line Items in the Control Panel #727 #728

## v4.0.2 (2022-09-24)

### What's fixed

-   Fixed display issues when using SC's fieldtypes in listing tables #723 #724
-   Fixed stock being decreased on localised product entries when Stock field isn't localised #721 #725

## v4.0.1 (2022-09-13)

### What's fixed

-   When re-ordering variants, the prices will no longer be shuffled about #719 #720

## v4.0.0 (2022-09-10)

### What's new

-   There's been a few coupon-related improvements:
    -   You can now disable coupons #713
    -   You can now set coupons to expire after a certain date #716
    -   The 'Customers' field on the Coupons publish form will now change, depending on the current customer driver (eg. entries, users, database). #714
-   Under the hood, we're now taking advantage of PHP 8.1 enums #712

### What's changed

-   Simple Commerce has dropped support for PHP 8.0 and Laravel 8. Leaving PHP 8.1 and Laravel 9 as the currently supported versions.
-   Coupons have moved out of collections, into their own 'thing' in the Stache. #705

You can read more about what's changed in the [upgrade guide](https://simple-commerce.duncanmcclean.com/upgrade-guides/v3-x-to-v4-0), along with steps on how to upgrade.

## v3.2.20 (2022-09-03)

### What's fixed

-   Fix overview widgets not loading (#707)

### What's improved

-   Implement contracts for tax repositories

## v3.2.19 (2022-09-01)

### What's new

-   Simple Commerce will now show a 'Customers' nav item if you're using the user customer driver. #702 #703

### What's fixed

-   When using the user customer driver in combination with database users, there was an issue where a 'super user' check would error out. #702 #703

### What's improved

-   Removed usage of `parseLoop` method in Tags - it's not very performant and isn't really needed.

## v3.2.18 (2022-08-24)

### What's fixed

-   Fixed an issue with the Money fieldtype when no value is set #459 #700

## v3.2.17 (2022-08-19)

### What's fixed

-   The Money fieldtype no longer returns £0 when no value is set #699
-   `Product`s are now hydrated with inherited data too (multi-site) #697 #698

## v3.2.16 (2022-07-30)

### What's fixed

-   Fixed issues when users rename the 'Collections' nav item in the CP #692

## v3.2.15 (2022-07-28)

### What's fixed

-   Fixed an issue caused when you rename sections in the CP Nav #691

## v3.2.14 (2022-07-22)

### What's fixed

-   Fixed an issue in the upgrade script if you use users for your customers

## v3.2.13 (2022-07-19)

### What's new

-   Added `{{ sc:cart:itemsTotalWithTax }}` tag to get 'items total + tax total' #687 #688

## v3.2.12 (2022-07-08)

### What's fixed

-   Fixed `Undefined variable $site` error with email notifications (sometimes) #666 #683

## v3.2.11 (2022-06-20)

### What's improved

-   Updated default order blueprint for Eloquent database orders #678

### What's fixed

-   Fixed issue where after removing a line item, items array would end up with keys #676 #677

## v3.2.10 (2022-06-20)

### What's fixed

-   Fixed an issue where a customer would see a 'checkout complete' message even if they cancelled checking out via Mollie. #674 #675

## v3.2.9 (2022-06-17)

### What's new

-   Form Parameter Validation can now be disabled, if needed #670

### What's improved

-   Adjusted copy of 'Price includes tax' toggle on tax rates

### What's fixed

-   Fixed issues where additional customer information would not be saved in some cases #658 #661
-   Fixed an issue with off-site gateways, where you'd see an empty order after checking out #664 #671
-   Added some code to ensure the tax rate on line items are saved as integers, rather than strings

## v3.2.8 (2022-06-15)

### What's fixed

-   Fixed permission issues on Simple Commerce's CP Nav #657 #659
-   Fixed rounding calculation issues when using coupons #651 #660

## v3.2.7 (2022-06-09)

### What's improved

-   Added support for underscore cart tag parameters #650

### What's fixed

-   Fixed some issues with coupon calculations when using float/decimal values #651 #655

## v3.2.6 (2022-06-07)

### What's fixed

-   Database Orders: Fixed an issue where customers & coupon's weren't being saved on orders correctly #648

## v3.2.5 (2022-06-03)

### What's improved

-   Improved the layout of the default Orders blueprint

#### What's fixed

-   Refactored the `{{ sc:customer:orders }}` tag to fix a couple of issues #645 #646
-   Fixed an issue where Debugbar would create orders even if you don't need it to. #647

## v3.2.4 (2022-06-03)

### What's fixed

-   Fixed an issue post-install when using a language other than English

## v3.2.3 (2022-06-03)

### What's fixed

-   Fixed another cause of 'ghost orders'

## v3.2.2 (2022-06-02)

### What's fixed

-   Actually add the upgrade script to the list of upgrade scripts..

## v3.2.1 (2022-06-02)

### What's fixed

-   Actually add the upgrade script to the list of upgrade scripts..

## v3.2.0 (2022-06-02)

**Note:** During the update process, the 'Value' field on your coupon blueprint will be renamed to `coupon_value`.

### What's new

-   Added a `product` parameter to the `{{ sc:cart:updateItem }}` and `{{ sc:cart:removeItem }}` tags #644
-   Added a `{{ sc:gateways:count }}` tag that returns a count of available gateways

### What's fixed

-   Renamed the 'Value' field on coupons to `coupon_value` (to avoid conflicts with Statamic) #642
-   Squashed the bug that caused 'ghost orders' to be created 👻
-   Fixed a bug with the `{{ sc:customer:orders }}` tag #641
-   Fixed a bug where the data from the PayPal Gateway wasn't being saved properly #643
-   Checking out via an off-site gateway will now redeem coupons & add the order to the customer's list of orders

## v3.1.0 (2022-05-24)

### What's new

-   Support for custom database columns on Order/Customer tables #634 #637

### What's fixed

-   Fixed PSR-4 warnings #635 #636
-   Removed gateway name from back-office order emails

## v3.0.4 (2022-05-16)

### What's fixed

-   Fixed an issue where the order number would be regenerated after every save #632

## v3.0.3 (2022-05-13)

### What's fixed

-   Fixed `sc:cart:cleanup` command when storing orders as entries #631

## v3.0.2 (2022-05-12)

### What's fixed

-   Fixed a few issues when checking out with an existing customer #629 #630

## v3.0.1 (2022-05-07)

### What's fixed

-   Database orders: Fixed an issue where you'd end up with multiple orders when creating a new one #627
-   Session Cart Driver: If there's no current cart, create one.
-   Fixed an issue where you could get a 'stale' version of the cart when using the `{{ sc:cart:addItem }}` tag with AJAX.

## v3.0.0 (2022-04-28)

### What's new

-   Support for storing [orders & customers in a database](https://simple-commerce.duncanmcclean.com/database-orders) #599
-   Brand new 'Overview' CP page for a top-level view of your store #620
-   Brand new Gateway fieldtype #609
-   Brand new Shipping Method fieldtype #611
-   Statamic's new-ish ['title format'](https://statamic.dev/collections#titles) feature is now used to generate titles for order/customer entries #601
-   You can now use Simple Commerce's cart tags on the Order Confirmation page #605
-   Gateways may return their own validation messages #598
-   Shipping Methods can now have their own config arrays #610

### What's improved

-   Huge improvements to order emails #603
-   Added some protection for 'hidden form parameters' #607
-   You may now specify a 'whitelist' of additional fields you wish to provide when submitting front-end forms #608
-   Order Numbers are stored in their own (hidden) field now #604
-   Improved the handling of first/last name fields
-   Line Items are no longer returned as arrays, they have their own DTO class now #612
-   Updated `moneyphp/money` to v4.0

### What's fixed

-   £0 will be returned instead of `null` if Money value is empty

### Breaking changes

There's quite a lot of breaking changes between v2.4 and v3.0. It's highly recommended you read the [upgrade guide](https://simple-commerce.duncanmcclean.com/upgrade-guide) before upgrading.

## v3.0.0-beta.7 (2022-04-26)

### What's improved

-   Cleanup default config file
-   Database Migrator now supports migrating from Users driver, not just from Customers collection
-   Implemented 'Top Customers' overview query for Users customer driver

### What's fixed

-   Fixed an issue when augmenting a blueprint containing fieldset imports
-   Fixed an issue saving coupons when using database orders
-   Fixed incorrect imports for Runway fields in blueprint
-   Make the Gateway fieldtype work with database orders
-   Fixed an ordering issue for 'Recent Orders' query on overview, when using database orders
-   When refunding an order via Stripe, get the Stripe Payment Intent ID from the right place..

## v3.0.0-beta.6 (2022-04-23)

### What's new

-   Brand new 'Overview' CP page for a top-level view of your store #620

### What's removed

-   Removed Dashboard 'Sales Widget'

## v3.0.0-beta.5 (2022-04-16)

### What's fixed

-   Removed the title field from Order & Customer blueprints (title formats - no need for them)
-   Gateway & Shipping Method fields are now marked as read-only when added via the upgrade script
-   Fixed an issue where collections were never created when running `sc:install` command
-   Fixed an issue where the upgrade script would grab the blueprint for the wrong collection when filling customer field whitelist during upgrade

## v3.0.0-beta.4 (2022-04-16)

### What's new

-   Shipping Method fieldtype #611

### What's improved

-   Eloquent Orders are marked as 'read-only' in the CP
-   Line Items are no longer returned as arrays, they have their own DTO class now #612
-   Gateway & Shipping Method fields are now automatically added to order blueprints during upgrade #613
-   Title formats are now configured when installing Simple Commerce with `sc:install` command #614
-   Customer data updated via the `{{ sc:customer:update }}` tag is now also whitelisted #615
-   The Stripe webhook code now handles refunds (but technically we don't support webhooks yet 👀)
-   Updated interfaces & facade hints #616
-   Drop `addOrder` method from `Customer` class #617
-   And some small other tidy up bits...

### What's fixed

-   £0 will be returned instead of `null` if Money value is empty

## v3.0.0-beta.3 (2022-04-13)

### What's new

-   Brand new Gateway Fieldtype #609
-   Shipping Methods can now have their own config arrays #610

### What's improved

-   Improved the handling of first/last name fields

## v3.0.0-beta.2 (2022-04-09)

### What's fixed

-   Improved performance when generating order numbers (entry driver)
-   Fixed an issue when using the `{{ total_including_tax }}` tag
-   Added a missing upgrade script to the list of upgrade scripts that should be run
-   Fixed an issue where the Mollie webhook would fail if you're using the Eloquent driver
-   Copied over the recent updates of the 'Order Status' filter in the CP
-   Eloquent driver: Store customer name in the database
-   Eloquent driver: Show list of customer's orders when viewing a customer in the CP
-   Eloquent driver: Fixed an issue where `customer_id` would not be set
-   Eloquent driver: The cart cleanup command is now compatible with the Eloquent driver

## v3.0.0-beta.1 (2022-04-09)

The first of a few beta releases of Simple Commerce v3.0! There's quite a lot of breaking changes between v2.4 & v3.0, so I'd highly recommend you read the [upgrade guide](./docs/upgrade-guide.md) before upgrading.

PS: A proper list of what's new/improved is available in the [draft v3.0 changelog](https://github.com/duncanmcclean/simple-commerce/blob/main/CHANGELOG.md#v300-2022-xx-xx).

## v2.4.11 (2022-04-26)

### What's fixed

-   Stripe: Payment Intent ID is now saved properly (meaning refunds will now work) #624 #625 by @duncanmcclean

## v2.4.10 (2022-04-25)

### What's fixed

-   Fixed an issue where checkout would fail OR a customer would get charged incorrectly when using a coupon with `maximum_uses` set #622

## v2.4.9 (2022-04-05)

### What's new

-   Added a `{{ total_including_tax }}` tag to help you figure out the Line Item Total + Tax #602

### What's fixed

-   Made some performance improvements to the way order numbers are generated.

## v2.4.8 (2022-04-02)

### What's new

-   Added `{{ sc:cart:free }}` tag to check if an order is free

### What's improved

-   The Receipt PDF will contain the site name, rather than random admin information
-   Back-office email has some bold text now

## v2.4.7 (2022-03-31)

### What's new

-   Added 'Refunded' option to Order Status filter

### What's fixed

-   The 'Refund' action can now be used when overriding default `Order` class
-   Improved exception handling for refunds
-   Fixed an issue around refunds not working as expected

## v2.4.6 (2022-03-30)

### What's improved

-   You can now filter by 'Shipped' orders via the Order Status filter in the CP #597 by @duncanmcclean

## v2.4.5 (2022-03-28)

### What's new

-   Ability to customise the 'Payment Intent Data' sent to Stripe #592 by @duncanmcclean

## v2.4.4 (2022-03-21)

### What's new

-   Added a 'Simple Commerce' tab to Laravel Debugbar #590 by @duncanmcclean

## v2.4.3 (2022-03-18)

### What's new

-   You can now mark orders as 'Shipped' #588 by @steffenschmidt

## v2.4.2 (2022-03-17)

### What's fixed

-   Removed an upgrade script which could potentially cause issues with sites with large amounts of orders.

## v2.4.1 (2022-03-15)

### What's fixed

-   _Actually_ support Laravel 9

## v2.4.0 (2022-03-15)

**After a long wait, Simple Commerce v2.4 is finally here!!**

Please ensure you read the [upgrade guide](https://simple-commerce.duncanmcclean.com/upgrade-guide) before upgrading. It notes how to update & any breaking changes you might want to know about.

### What's new

-   Brand new Tax Engine ✨ #438
-   Regions - ties into tax, allowing you to specify an area of a country (eg. Scotland, UK)
-   Ability to set a 'default shipping method' #555 #562
-   Statamic 3.3 compatibility #580

### What's improved

-   Gateway data has been combined, under a single key #498
-   'Temporary' gateway data will now be cleared after checking out
-   Shipping Methods can now access the order when checking availability
-   Multisites: each site will now have it's own cart (rather than sharing one between them) #519
-   The `PreCheckout` and `PostCheckout` events now includes the context of the request

## v2.4.0-beta.9 (2022-03-12)

### What's improved

-   Tidied up the default Simple Commerce config
-   All new sites will use the Cookie Cart driver 🍪
-   An event will now be dispatched by gateways if a payment fails
-   Implemented webhooks in the Stripe Gateway (you don't have to use them though - only when using Stripe Elements)

### What's fixed

-   Added proper validation rules to the Country & Region fieldtypes
-   (v2.3) Fixed an issue when removing an item from a cart, where the items would end up with keys (which could break stuff) #585

## v2.4.0-beta.8 (2022-03-05)

### What's improved

-   When viewing tax rates, it's now clearer which category that rate is assosiated with
-   Country & regions are now alphabetically sorted when using their respective tags
-   Updated built-in order blueprint (there were some duplicate fields in their before)

### What's fixed

-   The 'Line Items Tax' field is now properly hidden in the CP
-   The default tax rate will be 0%, not `null` like before 🤦‍♂️
-   If no tax zone is found to match the customer's address, it'll now (properly) fallback to the 'Everything' tax zone
-   When editing a tax rate, the 'tax zone' dropdown will no longer autoselect the first in the list
-   Fixed the 'included in price' config option not working on tax rates

## v2.4.0-beta.7 (2022-03-03)

### What's fixed

-   Reverted back to using the `default` error back, rather than our own. #582

## v2.4.0-beta.6 (2022-03-02)

### What's improved

-   After creating a tax zone/tax rate/tax category, you'll now be redirected to the index page, not the edit page

### What's fixed

-   Fixed an issue when trying to save the 'Everywhere' tax zone
-   Fixed an issue where it would assume the wrong ID for the default tax rate
-   Simple Commerce errors now use their own error bag & `{{ sc:hasErrors }}` should _ACTUALLY_ give you the expected answer now.

## v2.4.0-beta.5 (2022-02-28)

> This is a re-tag of `v2.4.0-beta.4` which had some issues during the build pipeline. It was easier to simply issue a fresh build.

Everything from the latest in v2.3 and...

### What's new

-   Statamic 3.3 compatibility #580 by @duncanmcclean
-   Added 'Line Items Tax' fieldtype to allow for augmentation of line item tax information #579 by @duncanmcclean

### Breaking changes

-   Dropped support for Statamic 3.1

## v2.4.0-beta.4 (2022-02-28)

Everything from the latest in v2.3 and...

### What's new

-   Statamic 3.3 compatibility #580 by @duncanmcclean
-   Added 'Line Items Tax' fieldtype to allow for augmentation of line item tax information #579 by @duncanmcclean

### Breaking changes

-   Dropped support for Statamic 3.1

## v2.4.0-beta.3 (2022-02-05)

Everything from the latest in v2.3 and...

### What's new

-   Ability to set a 'default shipping method' #555 #562

## v2.4.0-beta.2 (2021-12-08)

Everything from the latest in v2.3 and...

### What's new

-   Ability to limit coupons to certain customers

### What's improved

-   'Temporary gateway data' will now be cleared from orders after checkout

## v2.4.0-beta.1 (2021-12-17)

Simple Commerce v2.4 is on it's way - this is the very first beta release! The plan is to have a few beta releases for folks to test, then I'll release during the New Year!

### Updating to the beta

-   Set `minimum-stability` to `"dev"` or `"alpha"`

-   Change `"doublethreedigital/simple-commerce"` to `"2.4.*"`

```json
// composer.json

"minimum-stability": "alpha",
"require": {
    "doublethreedigital/simple-commerce": "2.4.*",
    // all the other stuff...
},
```

-   Then run: `composer update doublethreedigital/simple-commerce --with-all-dependencies`

After this, I'd recommend running through the [upgrade guide](https://github.com/duncanmcclean/simple-commerce/blob/main/docs/upgrade-guide.md) which explains any changes you may need to make.

### Full Changelog

For more information on what's new/what's changed, review the [`CHANGELOG.md`](https://github.com/duncanmcclean/simple-commerce/blob/main/CHANGELOG.md).

---

## v2.3.70 (2022-03-11)

### What's fixed

-   Fixed an issue when removing an item from a cart, where the items would end up with keys (which could break stuff) #585

## v2.3.69 (2022-03-03)

### What's fixed

-   Validation errors use the `default` error bag, so SC shouldn't try and use its own when pulling out errors #581 #583 by @duncanmcclean

## v2.3.68 (2022-02-23)

### What's new

-   Order Receipts are now attached to the back-office confirmation email #569 #577 by @duncanmcclean

## v2.3.67 (2022-02-22)

### What's fixed

-   Fixed a compatibility issue with PHP 8.1 and the package we use for Currency formatting #575 #576 by @duncanmcclean
-   Fixed 'Division by zero' issue when generating receipts #576 by @duncanmcclean

## v2.3.66 (2022-02-15)

### What's fixed

-   Added validation to ensure email addresses with spaces don't work #564 #565 #567 by @duncanmcclean

## v2.3.65 (2022-02-03)

### What's fixed

-   Squashed an error that would appear when a coupon was being augmented #558 #559

## v2.3.64 (2022-01-22)

### What's new

-   Need an order's receipt URL in the front-end? Just grab for `{{ receipt_url }}` and be done with it! #550

## v2.3.63 (2022-01-17)

### What's new

-   You may now configure if you'd like the metadata to be unique or not in line items #546

## v2.3.62 (2022-01-11)

### What's fixed

-   Fixed an error when you have no 'option fields' configured on your Product Variants field #542

## v2.3.61 (2022-01-08)

### What's new

-   Added a new `{{ sc:cart:alreadyExists }}` tag for checking if a product exists in the cart

### What's improved

-   We're now telling Gateways which version of Simple Commerce is being used to send API requests

### What's fixed

-   Fixed compatibility issues with the 'Order Status' filter in the CP

## v2.3.60 (2022-01-07)

### What's fixed

-   Fixed an issue with Off-site Gateways and the `PostCheckout` event #535 #536
-   Added a check to fix an issue with Mollie Webhook, where if it fails, it fails continuously

## v2.3.59 (2022-01-03)

### What's improved

-   When setting up a multisite, developers will see a more helpful exception reminding them to add the site to their Simple Commerce config #524

### What's fixed

-   Currency formatting on the Sales Widget has been fixed for certain currencies #527 #529
-   CP Listings will now show the right currency on Money fields (only an issue where there's multiple sites) #523 #526

## v2.3.58 (2021-12-21)

### What's new

-   Country names are now translatable! #522

## v2.3.57 (2021-12-20)

### What's fixed

-   Fixed issue when using `UserCustomer` and Eloquent users together

## v2.3.56 (2021-12-13)

### What's fixed

-   Fixed issue where selected images would not be shown after save on Product Variants fieldtype #511

## v2.3.55 (2021-12-13)

### What's fixed

-   The 'Stock' feature will now work when using an off-site gateway #506 #509
-   Fixed issue with custom option fields not working before initial save #503 #510
-   The `name` parameter will no longer be stripped when using form tags #505
-   When updating line items, the quantity will always be saved as an integer, not a string.

## v2.3.54 (2021-11-30)

### What's fixed

-   Removed `ray` call from Variants fieldtype which would cause errors #496 by @ryanmitchell

## v2.3.53 (2021-11-20)

### What's improved

-   Improved the display of the 'Product Variants' fieldtype #494

### What's fixed

-   Fixed an issue where the Assets & Toggle fieldtypes (and probably others) were not behaving properly #493

## v2.3.52 (2021-11-13)

### What's new

-   Added a new `PreventCheckout` exception which will cancel a checkout attempt if thrown
-   `PreCheckout` & `PostCheckout` events now both contain the Checkout request

### What's fixed

-   Fixed `{{ sc:errors }}` tag not returning what it should.

## v2.3.51 (2021-11-09)

### What's improved

-   The `make:gateway` command now allows you to choose between generating an on-site and an off-site gateway #490
-   You can now register shipping methods on-demand (outwith the config file) with `SimpleCommerce::registerShippingMethod()` #489

## v2.3.50 (2021-10-29)

### What's fixed

-   Made fixes for when using alongside the Eloquent driver #486

## v2.3.49 (2021-10-23)

### What's new

-   Added `toPence` and `toDecimal` methods to the `Currency` facade

## v2.3.48 (2021-10-15)

### What's fixed

-   Fixed issue with variant stock checks if you have 'dynamic variants' (eg. ones that don't actually exist on the product but are made up on the fly)
-   Fixed an issue where shipping address would fail to exist if you were using `billing_address_line1` instead of `billing_address`

## v2.3.47 (2021-10-14)

### What's new

-   Added Region fields to addresses #483

### What's fixed

-   Everything in the default Order blueprint is now read-only
-   Product variant fieldtype now respects being `read_only`

## v2.3.46 (2021-10-09)

### What's new

-   Ability to dynamically change prices of products when they're added to the cart #479

## v2.3.45 (2021-10-06)

### What's fixed

-   Fixed an issue when checking out a product with 1 item left in stock #477 #478

## v2.3.44 (2021-10-05)

### What's fixed

-   Fixed an issue checking out with variants with stock

## v2.3.43 (2021-10-02)

### What's new

-   Variants can now hold stock counts. [Review docs](https://simple-commerce.duncanmcclean.com/stock) #474

## v2.3.42 (2021-09-30)

### What's new

-   Added `only`, `exclude` and `common` parameters to the `{{ sc:countries }}` tag, [see docs](https://simple-commerce.duncanmcclean.com/tags/countries) #473

### What's fixed

-   Fixed an issue where Simple Commerce tried to merge a collection and an array.
-   Moved away from a deprecated Statamic Core method statamic/cms#4298

## v2.3.41 (2021-09-21)

### What's new

-   An on-site mode for the PayPal gateway #472

## v2.3.40 (2021-09-20)

### What's fixed

-   Fixed some PayPal webhook related bugs #471
-   Money fieldtypes will no longer return £0 when augmented if no price is saved #459
-   Improved docblocks for `Gateway` facade

## v2.3.29 (2021-09-18)

### What's fixed

-   Fixed a dirty state issue with the Product variants fieldtype #461
-   Only the Transaction ID is now stored as `paypal` data on the order entry

## v2.3.28 (2021-09-17)

### What's fixed

-   Fixed a bug where `paypal` data wasn't being saved properly on the order entry (causing the callback to not work) #469

### What's new

-   Customer and address information will now be added to order when coming back from PayPal #469

## v2.3.27 (2021-09-15)

### What's fixed

-   Fixed issue when you're being redirected back from an off-site gateway, where it would be unable to find the related order.

## v2.3.26 (2021-09-14)

### What's new

-   Added `{{ sc:cart:quantityTotal }}` tag to get total quantity of all items in cart #468

## v2.3.25 (2021-09-10)

### What's fixed

-   Customers are now marked as published by default, fixes #463

## v2.3.24 (2021-09-07)

### What's fixed

-   Receipt data is now being augmented, meaning prices and product name's should display properly now #460
-   The `prepare` method will no longer be called for off-site gateways when using the `{{ sc:checkout }}` tag #462

## v2.3.23 (2021-09-06)

### What's fixed

-   The Country fieldtype no longer gives array errors when `max_items` is set to `1`

## v2.3.22 (2021-09-06)

### What's fixed

-   Fixed calculator so coupon total includes tax total #458
-   When you clear the 'Money' fieldtype's value, we'll now save it as `null`, rather than 0 #459

## v2.3.21 (2021-08-30)

### What's new

-   You can now pass in customer information when adding items to the cart
-   You can now limit product purchases to only customers who have purchased a specific product #452

### What's fixed

-   Coupons total is now based on the line items total, not like grand total #453

## v2.3.20 (2021-08-27)

### What's fixed

-   Fixed issue where field meta was not being passed into the 'Product Variants' fieldtype #454

## v2.3.19 (2021-08-19)

### What's new

-   Support for [Statamic 3.2](https://statamic.com/blog/statamic-3.2-beta)
-   Smarten'd up the `{{ sc:coupon }}` tag, so you can do things like `{{ sc:coupon:minimum_cart_value }}`

### What's fixed

-   If `maximum_uses` was `null` on a coupon, you would be unable to redeem it
-   `gateway_config` went missing somewhere and it's documented, so we added it back

## v2.3.18 (2021-08-10)

### What's fixed

-   Fixed an issue where notifications were not being sent properly #451

## v2.3.17 (2021-08-03)

### What's fixed

-   Fixed an issue where if the order value was `0` the `OrderPaid` event would not be dispatched (causing issues with notifications, etc)

## v2.3.16 (2021-07-31)

### What's fixed

-   Fixed an issue where the `prepare` method on gateways would still be loaded even if the value of an order was `0.00`

## v2.3.15 (2021-07-28)

### What's fixed

-   Fixed tax calculations when `price_includes_tax` is `false` #449

### What's improved

-   Added some better testing around the Stripe Gateway

## v2.3.14 (2021-07-17)

### What's new

-   Added a new 'User Customer' driver so you can use your users as customers, not a seperate collection

## v2.3.13 (2021-07-12)

### What's fixed

-   Added `environment` config for PayPal gateway (otherwise we'd always be in sandbox 🤦‍♂️)
-   Fixed issue where the Refund action would cause issues if the Order driver isn't the default

## v2.3.12 (2021-07-08)

### What's new

-   Built-in PayPal Gateway (docs coming soon!)

## v2.3.11 (2021-07-07)

I've been doing some 'dog-fooding' of Simple Commerce at work over the last couple of days and I've found quite a few bugs, so this release is a big pile of fixes.

### What's improved

-   If a product is out of stock, we'll now give you a validation error and remove the item from the cart.
-   Getting data from the cart tag, like so: `{{ sc:cart:something }}` will now go through augmentation
-   Stripe will now show API requests as coming from Simple Commerce (instead of directly through the Stripe SDK)

### What's fixed

-   Fixed coupon redeemed/maximum uses check
-   The Coupon total will now calculate properly when using a non-entry driver
-   Fixed `{{ sc:coupon:has }}` when using a non-entry driver
-   Tided up some code and added some null checks in places
-   Fixed Stripe refunds not working properly

## v2.3.10 (2021-07-06)

### What's new

-   Added a new `currency` modifier

### What's fixed

-   Receipts now work when using a custom order class #443
-   `_request` will no longer be saved when checking out.

## v2.3.9 (2021-07-06)

### What's fixed

-   When using a custom order class, `sc:cart:count` would not return the correct total. #442
-   When using a custom order class, you wouldn't be able to add to the cart #441
-   If you don't have notifications for a trigger, it won't error now

## v2.3.8 (2021-06-29)

### What's fixed

-   Fixed small bug with notification improvements from the other day

## v2.3.7 (2021-06-26)

### What's fixed

-   Updated the gateway stub so it's up-to-date
-   `_request` will no longer be saved to the order entry when saving.

### What's new

-   New Country fieldtype
-   When creating an entry, we'll now save any default fields from your blueprint. #433
-   Both `StockRunningLow` and `StockRunOut` events are now available triggers for notifications. #423

## v2.3.6 (2021-06-11)

## What's fixed

-   When updating a line item, the metadata is no longer overwritten #431

## What's improved

-   You can now use a custom request on the `{{ sc:cart:addItem }}` and `{{ sc:cart:updateItem }}` tags #432

## v2.3.5 (2021-06-10)

## What's fixed

-   You can now set line item metadata when adding a new line item.

## v2.3.4 (2021-06-09)

### What's fixed

-   Fixed tax calculations if result is a rounded number, with no decimals #429

### What's improved

-   'United States' and 'Canada' aren't at the top of the countries list anymore
-   Removed `sc:info` command - it's never been used

## v2.3.3 (2021-06-05)

## What's new

-   You can now specify a Form Request on some form tags (for custom validation rules) #425

## v2.3.2 (2021-06-03)

### What's improved

-   Refactored usage of `findBySlug`, it's being deprecated soon and will likely be removed in Statamic 3.2 #424
-   When using `Something::create`, you can now provide `slug` or `published` to manually set the slug/published status.
-   Checkout: Payment is now done at the very end of the checkout request, rather than just before the coupon. Orders will also be recalculated before payment in case of any last-minute changes.
-   Checkout: You can now set the Customer's ID in the checkout request
-   Checkout: You can now redeem coupons as part of the checkout request
-   Checkout: Refactored some stuff around product stock
-   Checkout: Added a job lot of automated tests to cover the checkout flow

### What's fixed

-   Fixed IDE completion on the `Coupon` facade (was suggesting Order methods, rather than Coupon methods)
-   Checkout: Free orders will now be marked as paid again after checkout
-   Fixed issue where coupons limited to certain products could be valid/non-valid accidentally.

## v2.3.1 (2021-05-17)

### What's new?

-   You can now grab 'raw' data through the Cart Tag, rather than augmented data. (`{{ sc:cart:rawGrandTotal }}`)

## v2.3.0 (2021-05-10)

While there's been quite a few breaking changes between v2.2 and v2.3, most of them have been addressed by [Update Scripts](https://statamic.dev/knowledge-base/configuring-update-scripts#what-are-update-scripts), which will be run automatically when updating Simple Commerce.

Simple Commerce v2.3 requires your site to be running Statamic 3.1 and configured correctly for update scripts.

Please review the [Upgrade Guide](https://simple-commerce.duncanmcclean.com/update-guide) before updating.

### What's new

-   It's now easier to swap out the driver being used for products, orders, coupons or customers.
-   You can now mark any unpaid orders as paid from inside the Control Panel.
-   Events have been renamed and parameters have been switched about.
-   Notifications have been refactored! (Again...)
-   The `Address` DTO now contains some more helpful methods.
-   Product Variants now have their very own DTO
-   You can now filter by Order Status in the CP

### Breaking changes

-   Translations have been simplified. All translations live in the `messages.php` file. If you override the translations, please review.
-   Built-in gateways have been moved from `Gateways\GatewayName` to `Gateways\Builtin\GatewayName`
-   Gateway DTOs are now called `Response`, `Purchase` and `Prepare` (Gateway is no longer in the name)
-   Updates have been made to Data Contracts, please review if you are overriding any of them.
-   If you're overriding any of the Data Classes, please register it inside the updated config file, rather than manually via the Service Container.
-   `Cart` facade has been removed (it was deprecated in v2.2). Please replace with the `Order` facade.
-   Event parameters & event names have been changed. Please review if you are listening for any Simple Commerce events.
-   Notifications have been refactored - they now use Laravel Notifications, rather than Mailables. If you were overriding the notifications previously, you will need to refactor into Notifications and update inside the Simple Commerce config.

## v2.3.0-beta.5 (2021-05-03)

### What's fixed

-   500 error on Mollie Webhook with successful payment #422

## v2.3.0-beta.4 (2021-05-03)

### What's fixed

-   `Trailing data` error would sometimes appear when viewing paid orders in the CP

## v2.3.0-beta.3 (2021-04-30)

### What's fixed

-   Updated parameters of `CouponRedeemed` event.
-   Fixed issue where upgrade scripts would error if you're configuration is cached. #421

## v2.3.0-beta.2 (2021-04-23)

### What's new

-   The cart update request will respect any validation rules you have in your order blueprint #417

### What's fixed

-   Fixed an issue where update scripts wouldn't be run when upgrading to a beta release.

## v2.3.0-beta.1 (2021-04-22)

I've not got a solid date for v2.3 release yet but here's the first of a couple beta releases. Feel free to test it if you've got any spare time.

### Updating to the beta

-   Set `minimum-stability` to `"dev"` or `"alpha"`
-   Change `"doublethreedigital/simple-commerce"` to `"2.3.*"`

```json
// composer.json

"minimum-stability": "alpha",
"require": {
    "doublethreedigital/simple-commerce": "2.3.*",
    // all the other stuff...
},
```

-   Then run: `composer update doublethreedigital/simple-commerce --with-all-dependencies`

FYI: You'll need to be running Statamic 3.1 to install the beta.

### More info

For more information on what's new and any breaking changes, review the [`CHANGELOG.md`](https://github.com/duncanmcclean/simple-commerce/blob/2.3/CHANGELOG.md).

## v2.2.21 (2021-04-20)

-   [fix] Don't fail if user's cart has been deleted. Create a new one instead. #416

## v2.2.20 (2021-04-16)

-   [fix] Fixed exception when running Refund action on Order entry.
-   [fix] Tidied up the orders CP listing for new sites.

## v2.2.19 (2021-04-02)

-   [fix] Fix issues with coupon calculations #405

## v2.2.18 (2021-03-30)

-   [fix] Fixed issue where shipping & billing addresses would not be output on PDF Receipts #404

## v2.2.17 (2021-03-29)

-   [new] Statamic 3.1 support
-   [new] Product Specific Coupons #390
-   [new] Added docblocks to Facades #400
-   [new] Added country validation when submitting addresses #398 #402
-   [fix] Allow for calculator to be run with any `Order` class

## v2.2.16 (2021-03-13)

-   [fix] Fixed issue with `GatewayDoesNotExist` exception
-   [fix] Ensure we don't have two slashes in Gateway Webhook URL #387
-   [fix] Order Confirmation emails will now be sent for off-site gateways #395

## v2.2.15 (2021-03-10)

-   [new] Ability to bind your own `Calculator` class
-   [fix] Fixed bug where `items_total` would be a string when using coupons.
-   Refactored the `Calculator`

## v2.2.14 (2021-03-08)

-   [new] A new [`ReceiveGatewayWebhook`](https://github.com/duncanmcclean/simple-commerce/blob/main/src/Events/ReceiveGatewayWebhook.php) event is dispatched when a gateway webhook is received.
-   [new] You can now specify a different redirect URL for errors. - `error_redirect`.
-   [fix] Improved handling of Mollie webhooks, we now detect if an order has been paid and redirect correctly. #384
-   [fix] Fixed issue where cookie cart driver wasn't forgetting cart after checkout #383
-   [fix] An exception will be thrown when a gateway errors, instead of a die dump.
-   [fix] Fixed webhook and callback URLs sometimes not being formed correctly.
-   [fix] Fixed an occasionaly exception with the Cookie Driver.
-   Deprecated 'order item' methods, and replaced them with 'line item' methods.

## v2.2.13 (2021-03-04)

-   [new] Added [Ignition Solutions](https://flareapp.io/docs/solutions/introduction) to some exceptions to help with debugging.
-   [fix] Fixed the ability to update an existing cart item with a negative quantity #375
-   [fix] Fixed an incorrect method signature in the shipping method stub #380
-   Tidied up [the `README`](https://github.com/duncanmcclean/simple-commerce) (but it's not really code related)

## v2.2.12 (2021-02-22)

-   [new] Orders will now be added to Customer entries, so there's now a two-way relationship #369
-   [new] You can also now use `{{ sc:customer:orders }}` with Orders on the Customer entries, using the `from` parameter.
-   [fix] Fixed issue where email's would not be sent if email was set but no customer on order. #372

## v2.2.11 (2021-02-19)

-   [new] Added some helper methods to the `Address` object.
-   Added tests to the Order Calculator (not sure how we got this far without them)
-   And some general cleanup 🧹

## v2.2.10 (2021-02-19)

-   [fix] Tax amounts should no longer be off. Was previously using the wrong formula.

## v2.2.9 (2021-02-18)

-   Cookie Driver is now the default for new installs.
-   [fix] Fixed `Call to undefined method make()` when using cookie cart driver. #365

## v2.2.8 (2021-02-16)

-   [fix] Validate against the possibility of having negative line item quantities. #354
-   [fix] Fixed bug with `{{ sc:cart:{key} }}` usage.
-   [fix] Fixed bug when Order calculator is called on paid order.

## v2.2.7 (2021-02-12)

-   [fix] Fixed issue when adding more than a single item to your cart #353
-   [fix] When gateway's response isn't success, throw validation errors #352

## v2.2.6 (2021-02-10)

-   [new] Allow adding product to cart multiple times and up the quantity. #351
-   [fix] Now throws `EntryNotFound` exception when no entry can be found, instead of `OrderNotFound`. #349

## v2.2.5 (2021-02-09)

-   [fix] Fixed accidental bug introduced with cart driver fix in v2.2.4.

## v2.2.4 (2021-02-09)

-   [fix] Don't throw an exception on `cart` endpoint if no cart exists
-   [fix] Don't attempt to augment variant fieldtype if value is `null`
-   [fix] When customer's active cart is deleted, a new one will be created, instead of throwing an exception. #348

## v2.2.3 (2021-02-06)

-   [new] Added a command to automatically remove old cart entries. `php please sc:cart-cleanup`
-   [fix] Coupon total should be calculated from items total, not the grand total.
-   [fix] If grand total of cart is `0`, then don't do any gateway work
-   [fix] Strip out any decimals from product prices when added to cart
-   [fix] On the variant fieldtype: if there is no value, display an error message instead of spinner.

## v2.2.2 (2021-02-02)

-   Fixed bug when removing an item from your cart #346

## v2.2.1 (2021-01-31)

It didn't take me very long to find a bunch of bugs...

-   Fixed exception within upgrade tool when `stillat/proteus` isn't installed
-   Upgrader will no longer continue if `stillat/proteus` isn't installed
-   Stripe Gateway should pull key & secret from gateway config, not hard coded `.env` values
-   When processing checkout, don't attempt to save data if we don't have any.

## v2.2.0 (2021-01-31)

Before upgrading, please review the [upgrade guide](https://simple-commerce.duncanmcclean.com//update-guide) in case there's any changes you need to make.

### What's new

-   [Cart Drivers](https://simple-commerce.duncanmcclean.com//cart-drivers)
-   Under the hood codebase improvements

### What's fixed

-   Various bugs

## v2.1.35 (2021-01-30)

-   [fix] Actually use the new format when adding items to the cart
-   [fix] Fixed issue when clearing the cart

## v2.1.34 (2021-01-30)

-   [new] Updated the default order blueprint
-   [new] Added a new 'Product Variant' fieldtype to select a single variant

## v2.1.33 (2021-01-27)

-   [fix] Fixed some naming inconsistencies with postal/zip codes #343

## v2.1.32 (2021-01-21)

-   [fix] Fix situations where the tax totals would be wrong with certain tax rates #340

## v2.1.31 (2021-01-21)

-   [fix] ~~Fix situations where the tax totals would be wrong with certain tax rates #340~~

## v2.1.30 (2021-01-17)

-   [new] Improved action responses (including propper support for AJAX usage)

## v2.1.29 (2021-01-14)

-   [fix] Fixed issue with customer entry being overwritten by new carts. #337
-   [fix] Fixed situation where exception would be thrown if the 'Maximum Uses' field isn't set #338

## v2.1.28 (2021-01-11)

-   [new] Currency formatting now depends on current site locale, instead of always being `en_US`.
-   [fix] Fixed issue with tax calculations #331
-   [fix] Fixed Mollie Gateway issues and off-site gateway issues #334

## v2.1.27 (2021-01-11)

-   [fix] Fixed `->get()` parameter issue when using Mollie webhook. #332

## v2.1.26 (2021-01-09)

-   [fix] Sometimes tags were being called twice. Now it should just be once!
-   [fix] Fixed exception sometimes if attempting variant augmentation on a product with no variants.
-   [fix] Fixed issue where Gateway Webhook URL's were returned as `null`.

## v2.1.25 (2021-01-05)

-   [fix] Fixed the way we handle fallback URLs for off-site gateways #329

## v2.1.24 (2021-01-04)

-   [fix] Fixed exception thrown by Off-site gateway callback. #327
-   [fix] If a redirect is not provided for off-site gateway, user should be redirected to the homepage.

## v2.1.23 (2020-12-28)

-   [new] PHP 8 Support! #318
-   [fix] Product entries with variants should not have a standard `price` field.
-   [fix] The `has` method on Repositories will now return `null`, instead of throwing an exception about undefined indexes.

## v2.1.22 (2020-12-23)

-   [fix] Fix issues parsing `null` Money fields. Addresses issue from #323.

## v2.1.21 (2020-12-23)

-   [fix] Just get site with `Site::current()` inside Money Fieldtype, instead of getting the entries' locale.

## v2.1.20 (2020-12-21)

-   [fix] Fixed issue when passing `receipt_email` to Stripe

## v2.1.19 (2020-12-21)

-   [fix] Simplified the site detecting logic in the Money Fieldtype #319

## v2.1.18 (2020-12-18)

-   [fix] Fixed issue with locales in Money Fieldtype again.

## v2.1.17 (2020-12-18)

-   Re-tag of v2.1.16 (the release workflow broke)

## v2.1.16 (2020-12-18)

-   [fix] Fix issue where `locale()` is called on undefined, in Money Fieldtype.

## v2.1.15 (2020-12-12)

-   Remove Woodland

## v2.1.14 (2020-12-12)

-   [new] You can now enable automatic receipt emails from Stripe.
-   [new] You can now use a single address for an order, instead of different shipping and billing ones.
-   [new] You can now set display names for gateways. Useful for when you give the customer an option between them.
-   [fix] Fixed a bug causing type exceptions.
-   [fix] Ensure customer can't add more of a product than you actually have in stock.

## v2.1.13 (2020-12-05)

-   [new] Added some better error handling for form tags.
-   [fix] Issue where it couldn't find an 'index' gateway using the `{{ sc:gateways }}` tag? Now sorted!

## v2.1.12 (2020-11-29)

A whole lot of API related changes this release...

-   [new] It's now easier to get fields from your cart. How's `{{ sc:cart:delivery_note }}`?
-   [new] The Order Calculator has been moved out of the `CartRepository` and into it's own class. However, the `recalculateTotals` method on the `CartRepository` will continue to work for the time being.
-   [new] Introduced a new `OrderRepository` which will eventually replace the `CartRepository` (it's a breaking change so it won't happen until at least v2.2)
-   [new] Added `customer` method to `CartRepository`
-   [fix] Default order blueprint no longer has a SKU field on it.
-   [fix] `php please sc:install` command will now only publish blueprints and configuration file.

## v2.1.11 (2020-11-27)

-   [new] Add an `exempt_from_tax` field to products that you want to be exempt from tax.
-   [fix] Carts & Customers will be created in the correct site.
-   [fix] When created, customers will now be published instead of a draft.
-   [fix] Money Fieldtype will respect the site of the entry and display the correct currency.
-   [fix] Fixed issue where you could add the same product/variant to the cart twice.

## v2.1.10 (2020-11-22)

-   [fix] Fixed bug with blueprint field checking

## v2.1.9 (2020-11-22)

-   [new] Ensure fields are included in product & order blueprints.

## v2.1.8 (2020-11-21)

-   [fix] Fix `vendor:publish` bug when running `php please sc:install` (I should really double check this stuff before I release it)

## v2.1.7 (2020-11-21)

-   Re-tag of v2.1.6

## v2.1.6 (2020-11-21)

-   [update] Improved the install process - combine `vendor:publish` step and `setup-content`.

## v2.1.5 (2020-11-13)

-   [fix] A more descriptive message will now be shown if currency formatting fails due to the `php-intl` extension not being installed or enabled.'
-   [fix] Fixed issue where gateways weren't being booted at all...

## v2.1.4 (2020-11-12)

-   [fix] Fixed issue with [Woodland](https://github.com/duncanmcclean/simple-commerce/blob/main/src/Woodland.php) when installing Simple Commerce for the first time. #313
-   [fix] Fixed issue with product variants fieldtype on new product entries. #314
-   [fix] Fixed issue when adding a new variation in product variants fieldtype
-   [fix] Localize and use plural/signular stuff with index text for product variants fields.

## v2.1.3 (2020-10-30)

-   [fix] Fix issues with installing via Composer, from last release. Whoops.

## v2.1.2 (2020-10-30)

-   [new] Licensing statistics - so we can check how many sites are using Simple Commerce, what versions are in use and if they have valid licenses.

## v2.1.1 (2020-10-28)

-   [new] Simple Commerce fieldtypes will now display nicely in entry listings
-   [fix] Fixed issue when using an off-site gateway without specifing a `redirect`
-   Added a bunch of tests behind the scenes

## v2.1.0 (2020-10-18)

**v2.1 contains various breaking changes, we recommend reviewing [the update guide](https://simple-commerce.duncanmcclean.com//update-guide) to ensure your site will work with the update.**

-   [new][product variants](https://simple-commerce.duncanmcclean.com//product-variants)
-   [new][built-in mollie gateway](https://simple-commerce.duncanmcclean.com//gateways#builtin-gateways)
-   [new] Product Stock
-   [new] Sales Widget - dashboard widget for reviewing sales over a week, 14 days and a month.
-   [new] Support for Off-site gateways and a bunch of changes to the way gateways work overall.
-   [new] Minimum Order Numbers
-   [fix] Various bug fixes.

## v2.0.23 (2020-10-05)

-   [new] Licensing statistics - so we can check how many sites are using Simple Commerce, what versions are in use and if they have valid licenses. (also introduced in [v2.1.2](https://github.com/duncanmcclean/simple-commerce/releases/tag/v2.1.2))

## v2.0.22 (2020-10-04)

-   [new] You can now update the format of the customer titles.
-   [fix] When updating a customer, sometimes the title would be removed... #311
-   [fix] If updating a customer's name using `{{ sc:cart:update }}` tag, it wouldn't work because of some copy and pasting
-   And added a bunch of customer related tests...

## v2.0.21 (2020-10-04)

-   [fix] Fixed a bug that would happen if you try and get a customer that doesn't have a title or slug set.

## v2.0.20 (2020-10-04)

-   [fix] Fixed issue where SC would presume a customer entry had a name on it, but if it didn't then stuff would break.

## v2.0.19 (2020-10-04)

-   [fix] Fixed issue with customer data when being passed into Stripe Gateway, from #307

## v2.0.18 (2020-10-03)

-   [fix] Fixed issues when creating/updating customers via the `{{ sc:cart:update }}` tag. #307

## v2.0.17 (2020-09-30)

-   [fix] Fixed issue with decimals causing incorrect totals, when using coupons on an order #304

## v2.0.16 (2020-09-28)

-   [new] Payment Method's are now saved for later, with Stripe Gateway #306

## v2.0.15 (2020-09-05)

-   [fix] The money fieldtype will now default to 0 when nothing is entered instead of throwing an exception.
-   [fix] Fixed issue where you couldn't remove the last item from the cart

## v2.0.14 (2020-08-29)

-   [new] You can now register gateways on-demand with `SimpleCommerce::registerGateway(PayPalGateway::class, [])`
-   [fix] Fixed issue where deleting a single cart item would clear the cart instead (again) #293
-   [fix] Fixed issue when trying to submit checkout form without any customer information.

## v2.0.13 (2020-08-27)

-   [fix] Fixes issue when adding to the cart when you've already deleted items from the cart. #293
-   [fix] Generate a title and slug for customer if they don't already have one - fixes a type error #296
-   [fix] Fixed issue when the `CheckoutController` tries to call the `PreCheckout` event on case sensitive systems. #294

## v2.0.12 (2020-08-26)

-   [new] Brought back Order Statuses, you may want to run `php please simple-commerce:setup-command` to create the relevant taxonomies and terms.
-   [new] You can now send customer metadata when using `{{ sc:cart:update }}` and `{{ sc:checkout }}` tags. #289
-   [new] You can now toggle if Simple Commerce sends an `Order Confirmation` email to your customers after checkout. It's enabled by default. #288
-   [new] Customer & Order information is now passed to Stripe when preparing payment intent #292
-   [new] Brand new `php please simple-commerce:setup-command` command for setting up collections & taxonomies when installing Simple Commerce
-   [fix] Fixed issue where adding items to cart would overwrite what is already there. #290

## v2.0.11 (2020-08-24)

-   [fix] Fixed issue with ProductRepository not being bound properly because of a spelling mistake 🤦‍♂️ #287

## v2.0.10 (2020-08-22)

-   [new] You can now specify the version of the Stripe API you want to use.
-   [fix] Fixed issue caused when using a gateway that doesn't return anything from the prepare method.
-   [fix] Fixed `checkAvailability` failing for shipping methods
-   [fix] Fixed issue with completing cart without a customer being attached to the order.
-   Changed version constraint of `statamic/cms` due to v3 release

## v2.0.9 (2020-08-18)

-   [fix] Simple Commerce releases should now include built assets.
-   [fix] Issue when entering value inside Money fieldtype without separator and it converts it to cents/penies
-   [fix] Percentage coupons #281

## v2.0.8 (2020-08-17)

-   [fix] Simple Commerce tags were broken after beta 44

## v2.0.7 (2020-08-17)

-   [fix] Config, blueprint etc should no longer be overwritten on composer update
-   Simple Commerce only supports PHP 7.4

## v2.0.6 (2020-08-14)

-   [new] Refunds - somehow managed to ship without refunds but they're here now!
-   [new] The output from prepare methods in gateways is now saved in the order so it can be used again in the gateway
-   [break] Really small breaking change, inside the `{{ sc:gateways }}` loop, change `{{ config:* }}` to `{{ gateway-config:* }}` to grab gateway configuration values.
-   [fix] Fixed initial state for the money fieldtype, should no longer show .
-   [fix] Exceptions should now be thrown for when gateways don't exist or none is required at checkout
-   [fix] `_redirect` should no longer be passed into cart when updating cart
-   [fix] Fixed issue with coupon validation, where an error was being thrown as we were validation the wrong thing 🤦‍♂️ #276

## v2.0.5 (2020-08-11)

-   [fix] Blueprints being re-published after every Simple Commerce update

## v2.0.4 (2020-08-10)

-   [fix] Simple Commerce no longer relies on calebporzio/sushi for Currency and Country models
-   [fix] Incorrect typehint causing issues when getting cart items \*[fix] Typos

## v2.0.3 (2020-08-06)

-   [new] Introduced a `Product` facade, repository and related things...
-   [fix] Fixed bug where you'd run into an error if you visit the cart/checkout when you have no cart in the session. Fixes #275
-   [break] The `$request` variable is now passed in as a second parameter of the `purchase` method to a gateway.

## v2.0.2 (2020-08-01)

-   [new] Introduced two new commands: `make:gateway` and `make:shipping-method`
-   [fix] Use FormRequests for validating action endpoints
-   [fix] Officially only supports PHP 7.4
-   [break] Removed `/shipping-options` endpoints.
-   [break] Removed need for SKUs, you can have them if you want them but they won't be saved in orders anymore.

## v2.0.1 (2020-07-31)

-   [new] Added `PreCheckout` and `PostCheckout` events, triggered by the Checkout controller.

## 2.0.0 (2020-07-25)

-   **Simple Commerce v2.0 has launched!**
