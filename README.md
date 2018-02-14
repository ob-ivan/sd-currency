A collection of utility functions to enumerate available currencies and update their actual exchange rates.

Installation
============

```bash
composer require ob-ivan/sd-currency
```

Usage
=====

Registry
--------
A currency instance associates currency code with its representations in Unicode and as an HTML entity.

These currencies are supported:
- RUB
- EUR
- USD

You can enumerate available currencies using a registry instance:

```php
use SD\Currency\Model\Registry;

$registry = new Registry();
foreach ($registry->getAll() as $currency) {
    print "<p>The Unicode symbol for {$currency->getCode()} is {$currency->getUnicode()}</p>\n";
}
```

Or if you use dependency injection (see below):

```php
foreach ($this->getCurrency()->getRegistry()->getAll() as $currency) {
    ...
}
```

Formatter
---------
Use formatter service to format price according to given currency. Formatter instance uses a number
of configuration parameters.

```php
use SD\Currency\Model\Money;
use SD\Currency\Model\Registry;
use SD\Currency\Service\Formatter;

$registry = new Registry();
$formatter = new Formatter($registry, ['thousandSeparator' => '\'']);
// or using dependency injection:
$formatter = $this->getCurrency()->getFormatter($formatName);

echo $formatter->formatMoney(new Money(10000, $registry->getByCode('USD'))); // $ 10'000
echo $formatter->formatMoney(new Money(590000, $registry->getByCode('RUB'))); // 590'000 ₽
```

Repository & Store
------------------
All functions are available within repository instance which acts as a library facade:

```php
use SD\Currency\Repository;

$repository = new Repository($config);
```

Some of its methods require a store class to be configured:

```php
use SD\Currency\Repository;
use SD\Currency\Store\FileStore;

$repository = new Repository([
    'store' => FileStore::class,
    'args' => [
        'dir' => __DIR__ . '/currency_cache',
    ],
]);
$options = $repository->getOptions();
```

The store is used to retrieve currency rates and to persist them when updated (see Updater secion).

Two store implementations are provided by this library:
- `FileStore` uses a json file on hard drive.
- `ArrayStore` only keeps data in memory and is well suited for unit testing.

You can also implement `SD\Currency\Store\StoreInterface` to provide your own kind of store,
e.g. in database or memcache. Then pass its class name to `Repository` constructor as shown above.

Updater
-------
An updater service runs against the provided store and sets up exchange rates from the fixed source.
The official API of Central Bank of Russia is used by default. You can configure the updater with
your own XML source and XPath, as well as update interval (defaults to '1 day') if you don't want
to make too much requests to your source.

```php
use SD\Currency\Model\Registry;
use SD\Currency\Service\Updater;
use SD\Currency\Store\FileStore;

$registry = new Registry();
$store = new FileStore(__DIR__);
$updaterConfig = [
    'update_interval' => '30 minutes',
];
$updater = new Updater($registry, $store, $updaterConfig);
$updater->updateRates();
```

Or in case you use dependency injection:

```php
$this->getCurrency()->getUpdater()->updateRates();
```

Dependency Injection
--------------------
If you use `SD\DependencyInjection\Container` in your application,
consumers may import dependency injection trait to take advantage of the autodeclare feature:

```php
use SD\Currency\DependencyInjection\CurrencyAwareTrait;
use SD\DependencyInjection\AutoDeclarerInterface;
use SD\DependencyInjection\AutoDeclarerTrait;

class ExampleController implements AutoDeclarerInterface
{
    use AutoDeclarerTrait;
    use CurrencyAwareTrait;

    public function exampleAction()
    {
        return $this->render('example.twig', [
            'currencyOptions' => $this->getCurrency()->getOptions(),
        ]);
    }
}
```

Configuration
-------------
When using dependency injection you may provide a configuration file to set up your services:

```yaml
# config/currency.yaml
currency:
    store:
        class: App\Currency\Store
    formatter:
        class: App\Currency\Formatter
        myAwesomeFormat:
            thousandSeparator: ','
            symbolType: fontAwesome
            roundDirection: ceil
            roundDigits: 2
        myOtherFormat:
            ...
    updater:
        class: App\Currency\Updater
        config:
            url: https://money.example.com/
            xpath: //currency[code = "$code"]/rate
            updateInterval: 3 hours
```

Use `ConfigLoader` to populate a container with config values:

```php
use SD\Config\ConfigLoader;
use SD\Currency\DependencyInjection\CurrencyProvider;
use SD\DependencyInjection\Container;

$loader = new ConfigLoader('/path/to/config/dir');
$config = $loader->load();
$container = new Container(['config' => $config]);
$container->connect(new CurrencyProvider());
```

This will inject config values into corresponding services:

```php
$container->inject(function ($currency) {
    $store = $currency->getStore(); // instance of App\Currency\Store
    $formatter = $currency->getFormatter('myAwesomeFormat'); // uses config values
    $updater = $currency->getUpdater(); // uses config values
});
```

Development
===========
To run tests:

```bash
composer install
vendor/bin/phpunit
```
