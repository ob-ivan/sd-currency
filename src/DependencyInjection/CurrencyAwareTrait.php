<?php
namespace SD\Currency\DependencyInjection;

use SD\Currency\Repository;

trait CurrencyAwareTrait {
    protected $autoDeclareCurrency = 'currency';
    private $currency;

    public function setCurrency(Repository $currency) {
        $this->currency = $currency;
    }

    protected function getCurrency(): Repository {
        return $this->currency;
    }
}
