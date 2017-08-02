<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use SD\Currency\DependencyInjection\CurrencyProvider;
use SD\Currency\Repository;
use SD\DependencyInjection\Container;

class CurrencyProviderTest extends TestCase {
    public function testConnect() {
        $container = new Container();
        $provider = new CurrencyProvider();
        $container->connect($provider);
        $service = $container->get($provider->getServiceName());
        $this->assertInstanceOf(Repository::class, $service, 'Must return instance of Repository');
    }
}
