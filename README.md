A collection of utility functions to enumerate available currencies and update their actual exchange rates.

Installation
============

```bash
composer require ob-ivan/sd-currency
```

Usage
=====

Config
------
A currency config instance associates currency symbol (as an HTML entity) with currency code.

These currencies are supported:
- RUB
- EUR
- USD

You can enumerate available currencies like follows:

```php
use SD\Currency\Config;

foreach (Config::all() as $config) {
    print "<p>The symbol for {$config->getCode()} is {$config->getSymbol()}</p>\n";
}
```

Or if you use dependency injection (see below):

```php
foreach ($this->getCurrency()->getAllConfigs() as $config) {
    ...
}
```

Formatter
---------
Use formatter service to format price according to given currency. Formatter instance is configured
with a string to use as thousand separator:

```php
use SD\Currency\Service\Formatter;

$formatter = new Formatter(['separator' => '.']);
// or using dependency injection:
$formatter = $this->getCurrency()->getFormatter(['separator' => '.']);

echo $formatter->formatPrice('10000', '&#36;'); // $ 10.000
echo $formatter->formarPrice('590000', '&#8381;'); // 590.000 ₽
echo $formatter->formatPrice('n/a', ''); // n/a
```

Repository & Store
------------------
All functions are available within repository instance which acts as a library facade:

```php
use SD\Currency\Repository;

$repository = new Repository();
$config = $repository->getConfigByCode('EUR');
```

Some methods require a store instance to be provided to the repository:

```php
use SD\Currency\Repository;
use SD\Currency\Store\FileStore;

$repository = new Repository();
$store = new FileStore(__DIR__ . '/currency_cache');
$repository->setStore($store);
$options = $repository->getOptions();
```

The store is used to... ahem... store currency rates and update time. The available file store
implementation uses a json file on hard drive. You can implement `SD\Currency\Store\StoreInterface`
to provide your own kind of store, e.g. in database or memcache. See file store implementation
for the details.

Updater
-------
An updater service runs against the provided store and sets up exchange rates from the fixed source
which is Central Bank of Russia by default. You can provide it with your own XML source and XPath,
as well as update interval (defaults to '1 day') if you don't want to make too much requests to your source.

```php
use SD\Currency\Service\Updater;
use SD\Currency\Store\FileStore;

$store = new FileStore(__DIR__);
$updater = new Updater($store, [
    'update_interval' => '30 minutes',
]);
$updater->updateRates();
```

Or in case you inject store into repository at assemble time:

```php
$this->getCurrency()->getUpdater($updaterConfig)->updateRates();
```

Dependency Injection
--------------------
If you use `SD\DependencyInjection\Container` in your application, you may want to create your own
provider to handle custom store:

```php
use MyApp\Currency\RedisStore;
use SD\Currency\DependencyInjection\CurrencyProvider;

class CurrencyProvider extends CurrencyProvider {
    public function provide() {
        $currency = parent::provide();
        $currency->setStore(new RedisStore());
        return $currency;
    }
}
```

Development
===========
To run tests:

```bash
composer install
vendor/bin/phpunit
```
