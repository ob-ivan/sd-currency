<?php
namespace SD\Currency\DependencyInjection;

use SD\Currency\Repository;
use SD\DependencyInjection\ProviderInterface;

class CurrencyProvider implements ProviderInterface {
    public function getServiceName(): string {
        return 'currency';
    }

    public function provide() {
        return new Repository();
    }
}
