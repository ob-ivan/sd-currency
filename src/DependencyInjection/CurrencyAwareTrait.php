<?php
namespace SD\Currency\DependencyInjection;

use SD\Currency\Repository;

trait CurrencyAwareTrait {
    private $autoDeclareCurrency = 'currency';
    private $currency;

    public function setCurrency(Repository $currency) {
        $this->currency = $currency;
    }

    private function getCurrency(): Repository {
        return $this->currency;
    }
}
