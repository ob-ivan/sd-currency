<?php
namespace SD\Currency\DependencyInjection;

use SD\Config\ConfigAwareTrait;
use SD\Currency\Repository;
use SD\DependencyInjection\AutoDeclarerInterface;
use SD\DependencyInjection\AutoDeclarerTrait;
use SD\DependencyInjection\ContainerAwareTrait;
use SD\DependencyInjection\ProviderInterface;

class CurrencyProvider implements AutoDeclarerInterface, ProviderInterface
{
    use AutoDeclarerTrait;
    use ConfigAwareTrait;
    use ContainerAwareTrait;

    public function getServiceName(): string
    {
        return 'currency';
    }

    public function provide()
    {
        $repository = new Repository($this->getConfig('currency') ?? []);
        return $repository;
    }
}
