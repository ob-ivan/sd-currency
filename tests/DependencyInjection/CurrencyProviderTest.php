<?php
namespace tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use SD\Currency\DependencyInjection\CurrencyProvider;
use SD\Currency\Repository;
use SD\DependencyInjection\Container;

class CurrencyProviderTest extends TestCase
{
    public function testConnect()
    {
        $container = new Container([
            'config' => []
        ]);
        $provider = new CurrencyProvider();
        $container->connect($provider);
        $repository = $container->get($provider->getServiceName());
        $this->assertInstanceOf(Repository::class, $repository, 'Must return instance of Repository');
    }

    public function testStoreFromConfig()
    {
        $dir = '/path/to/data';
        $container = new Container([
            'config' => [
                'currency' => [
                    'store' => [
                        'class' => MockFileStore::class,
                        'args' => [
                            'dir' => $dir,
                        ],
                    ],
                ],
            ],
        ]);
        $provider = new CurrencyProvider();
        $container->connect($provider);
        $repository = $container->get($provider->getServiceName());
        $store = $repository->getStore();
        $this->assertInstanceOf(MockFileStore::class, $store, 'Store MUST be an instance of configured class');
        $this->assertEquals($dir, $store->getDir(), 'MUST inject argument value from config into store');
        $this->assertSame($container, $store->getContainer(), 'MUST inject services from container into store');
    }
}
