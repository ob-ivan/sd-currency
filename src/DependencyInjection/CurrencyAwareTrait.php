<?php
namespace SD\Currency\DependencyInjection;

use SD\Currency\Repository;

trait CurrencyAwareTrait {
    private $autoDeclareCurrency = 'currency';
    private $currency;

    public function setCurrency(Repository $currency) {
        $this->currency = $currency;
    }
}
