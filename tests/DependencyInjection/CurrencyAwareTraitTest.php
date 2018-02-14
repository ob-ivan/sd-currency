<?php
namespace tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use SD\Currency\DependencyInjection\CurrencyProvider;
use SD\DependencyInjection\Container;

class CurrencyAwareTraitTest extends TestCase
{
    public function testInheritAutoDeclare()
    {
        $container = new Container([
            'config' => [],
        ]);
        $provider = new CurrencyProvider();
        $container->connect($provider);
        $expectedService = $container->get($provider->getServiceName());
        $subclassConsumer = $container->produce(SubclassConsumer::class);
        $actualService = $subclassConsumer->getService();
        $this->assertSame($expectedService, $actualService, 'Subclass must inherit auto declare trait');
    }
}
