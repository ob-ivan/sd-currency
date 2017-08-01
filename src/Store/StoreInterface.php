<?php

namespace SD\Currency\Store;

interface StoreInterface {
    public function get(string $code): Record;

    /**
     * @param $code     string
     * @param $rate     float
     * @param $datetime DateTime
     *
     * TODO: Remove $datetime? now() is intended.
    **/
    public function set(string $code, float $rate, \DateTime $datetime);
}
