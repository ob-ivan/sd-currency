<?php

namespace SD\Currency;

trait AwareTrait {
    private $currency;

    public function setCurrency(Repository $currency) {
        $this->currency = $currency;
    }
}
