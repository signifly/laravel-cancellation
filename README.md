# Cancel models inside your Laravel app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/signifly/laravel-cancellation.svg?style=flat-square)](https://packagist.org/packages/signifly/laravel-cancellation)
[![Build Status](https://img.shields.io/travis/signifly/laravel-cancellation/master.svg?style=flat-square)](https://travis-ci.org/signifly/laravel-cancellation)
[![StyleCI](https://styleci.io/repos/119215413/shield?branch=master)](https://styleci.io/repos/119215413)
[![Quality Score](https://img.shields.io/scrutinizer/g/signifly/laravel-cancellation.svg?style=flat-square)](https://scrutinizer-ci.com/g/signifly/laravel-cancellation)
[![Total Downloads](https://img.shields.io/packagist/dt/signifly/laravel-cancellation.svg?style=flat-square)](https://packagist.org/packages/signifly/laravel-cancellation)

The `signifly/laravel-cancellation` package allows you to easily handle cancellation of your models. It is inspired by the SoftDeletes implementation in Laravel.

All you have to do to get started is:

```php
// 1. Add cancelled_at column to your table by using our macro cancellable
Schema::create('orders', function (Blueprint $table) {
    // ...
    $table->cancellable();
    // ...
});

// 2. Add the Cancellable trait to your model
class Order extends Model
{
    use Cancellable;
}
```

Here's a little demo of how you can use it after adding the trait:

```php
$order = Order::find(1);
$order->cancel();
```

You can query cancelled entities:

```php
$orders = Order::onlyCancelled()->get(); // returns all the cancelled orders
```

## Documentation
Until further documentation is provided, please have a look at the tests.

## Installation

You can install the package via composer:

```bash
$ composer require signifly/laravel-cancellation
```

The package will automatically register itself.

You can publish the config with:
```bash
$ php artisan vendor:publish --provider="Signifly\Cancellation\CancellationServiceProvider" --tag="config"
```

*Note*: If you set the exclude variable to true in your config, your query results will not include cancelled results by default (just like SoftDeletes).


This is the contents of the published config file:
```php
return [
    /**
     * Exclude the cancellations from the model's queries.
     * Will apply to all, find, etc.
     */
    'exclude' => false,
];
```

## Testing
```bash
$ composer test
```

## Security

If you discover any security issues, please email dev@signifly.com instead of using the issue tracker.

## Credits

- [Morten Poul Jensen](https://github.com/pactode)
- [All contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
