<?php
namespace SD\Currency\DependencyInjection;

use SD\Currency\Repository;
use SD\Currency\Store\StoreInterface;
use SD\DependencyInjection\DeclarerInterface;
use SD\DependencyInjection\ProviderInterface;

class CurrencyProvider implements DeclarerInterface, ProviderInterface {
    private $currencyStore;

    public function declareDependencies() {
        return ['currencyStore'];
    }

    public function setCurrencyStore(StoreInterface $currencyStore) {
        $this->currencyStore = $currencyStore;
    }

    public function getServiceName(): string {
        return 'currency';
    }

    public function provide() {
        return new Repository($this->currencyStore);
    }
}
