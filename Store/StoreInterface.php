<?php

namespace SD\Currency\Store;

use SD\Currency\Model\Option;

interface StoreInterface {
    public function get(string $code): Option;

    /**
     * @param $code     string
     * @param $rate     float
     * @param $datetime DateTime
     *
     * TODO: Remove $datetime? now() is intended.
    **/
    public function set(string $code, float $rate, \DateTime $datetime);
}
