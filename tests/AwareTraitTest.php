<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use SD\Currency\DependencyInjection\CurrencyProvider;
use SD\DependencyInjection\Container;

class AwareTraitTest extends TestCase {
    public function testInheritAutoDeclare() {
        $container = new Container();
        $provider = new CurrencyProvider();
        $container->connect($provider);
        $expectedService = $container->get($provider->getServiceName());
        $subclassConsumer = $container->produce(SubclassConsumer::class);
        $actualService = $subclassConsumer->getService();
        $this->assertSame($expectedService, $actualService, 'Subclass must inherit auto declare trait');
    }
}
