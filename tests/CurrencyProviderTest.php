<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use SD\Currency\DependencyInjection\CurrencyProvider;
use SD\Currency\Repository;
use SD\Currency\Store\ArrayStore;
use SD\DependencyInjection\Container;

class CurrencyProviderTest extends TestCase
{
    public function testConnect()
    {
        $container = new Container();
        $provider = new CurrencyProvider();
        $container->connect($provider);
        $repository = $container->get($provider->getServiceName());
        $this->assertInstanceOf(Repository::class, $repository, 'Must return instance of Repository');
    }

    public function testStoreFromConfig()
    {
        $container = new Container([
            'config' => [
                'currency' => [
                    'store' => [
                        'class' => ArrayStore::class,
                    ],
                ],
            ],
        ]);
        $provider = new CurrencyProvider();
        $container->connect($provider);
        $repository = $container->get($provider->getServiceName());
        $store = $repository->getStore();
        $this->assertInstanceOf(ArrayStore::class, $store, 'Store MUST be an instance of configured class');
    }
}
