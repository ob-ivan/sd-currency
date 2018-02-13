<?php
namespace SD\Currency\DependencyInjection;

use SD\Currency\Repository;
use SD\DependencyInjection\AutoDeclarerInterface;
use SD\DependencyInjection\AutoDeclarerTrait;
use SD\DependencyInjection\ContainerAwareTrait;
use SD\DependencyInjection\ProviderInterface;

class CurrencyProvider implements AutoDeclarerInterface, ProviderInterface
{
    use AutoDeclarerTrait;
    use ContainerAwareTrait;

    public function getServiceName(): string
    {
        return 'currency';
    }

    public function provide()
    {
        $config = $this->getConfig('currency');
        $repository = new Repository();
        if (isset($config['store']['class'])) {
            $repository->setStore($this->getContainer()->produce($config['store']['class']));
        }
        return $repository;
    }
}
