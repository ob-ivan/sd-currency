<?php

interface SD_Currency_Store_Interface {
    /**
     * @param $code string
     * @return SD_Currency_Option
    **/
    public function get($code): SD_Currency_Option;

    /**
     * @param $code     string
     * @param $rate     float
     * @param $datetime DateTime
     *
     * TODO: Remove $datetime? now() is intended.
    **/
    public function set($code, $rate, DateTime $datetime);
}
