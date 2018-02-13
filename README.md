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
$formatter = $this->getCurrency()->getFormatter(['thousandSeparator' => '\'']);

echo $formatter->formatMoney(new Money(10000, $registry->getByCode('USD'))); // $ 10'000
echo $formatter->formatMoney(new Money(590000, $registry->getByCode('RUB'))); // 590'000 ₽
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

The store is used to--ahem--store currency rates and update time. The available file store
implementation uses a json file on hard drive. You can implement `SD\Currency\Store\StoreInterface`
to provide your own kind of store, e.g. in database or memcache. See file store implementation
for details.

Updater
-------
An updater service runs against the provided store and sets up exchange rates from the fixed source
which is Central Bank of Russia by default. You can provide it with your own XML source and XPath,
as well as update interval (defaults to '1 day') if you don't want to make too much requests to your source.

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

Or in case you inject store into repository at assemble time:

```php
$this->getCurrency()->getUpdater($updaterConfig)->updateRates();
```

Dependency Injection
--------------------
If you use `SD\DependencyInjection\Container` in your application,
consumers may import dependency injection trait to take advantage of the autodeclare feature:

```php
use SD\Currency\DependencyInjection\CurrencyAwareTrait;
use SD\DependencyInjection\AutoDeclarerInterface;
use SD\DependencyInjection\AutoDeclarerTrait;

class ExampleController implements AutoDeclarerInterface {
    use AutoDeclarerTrait;
    use CurrencyAwareTrait;

    public function exampleAction() {
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
        myAwesomeFormat:
            thousandSeparator: ','
            symbolType: fontAwesome
            roundDirection: ceil
            roundDigits: 2
        myOtherFormat:
            ...
    updater:
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

```
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
