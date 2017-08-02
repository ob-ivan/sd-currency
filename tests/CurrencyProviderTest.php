<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use SD\Currency\DependencyInjection\CurrencyProvider;
use SD\Currency\Store\StoreInterface;
use SD\DependencyInjection\Container;

class CurrencyProviderTest extends TestCase {
    public function testConnect() {
        $container = new Container();
        $store = $this->createMock(StoreInterface::class);
        $container->register('currencyStore', $container->value($store));
        $provider = new CurrencyProvider();
        $container->connect($provider);
        $service = $container->get($provider->getServiceName());
        $this->assertEquals($store, $service->getStore(), 'Must inject store from container');
    }
}
