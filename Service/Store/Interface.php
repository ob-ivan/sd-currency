<?php

interface SD_Currency_Service_Store_Interface {
    public function get(string $code): SD_Currency_Model_Option;

    /**
     * @param $code     string
     * @param $rate     float
     * @param $datetime DateTime
     *
     * TODO: Remove $datetime? now() is intended.
    **/
    public function set(string $code, float $rate, DateTime $datetime);
}
