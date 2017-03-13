# Check Rollbar Limit
Artisan command to check rollbar limit

- setup rollbar access token in `config/services.php`
- add service provider of this package in your `config/app.php`

```php
'providers' => [
    // .. other providers
    Wzulfikar\CheckRollbarLimit\CheckRollbarLimitServiceProvider::class,
];
```
- run `php artisan rollbar:check-limit`

---
The command will send post request to rollbar and check if the request was rejected because of limit exceeded. And if so, event `\Wzulfikar\CheckRollbarLimit\RollbarLimitExceededEvent::class` will be triggered.

To listen to above event, you can add listener in you `EventServiceProviders.php`. Something like:

```php
// app/Providers/EventServiceProvider.php
protected $listen = [
    \Wzulfikar\CheckRollbarLimit\RollbarLimitExceededEvent::class => [
        // put your listener here
    ],
];
```
