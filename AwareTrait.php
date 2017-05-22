<?php

trait SD_Currency_AwareTrait {
    private $currency;

    public function setCurrency(SD_Currency_Repository $currency) {
        $this->currency = $currency;
    }
}
