<?php

class SD_Currency_Service_Store_File implements SD_Currency_Service_Store_Interface {
    const FILENAME = 'currencies.json';

    public function __construct($dir) {
        $this->filename = $dir . '/' . self::FILENAME;
    }

    /**
     * @param $code string
     * @return SD_Currency_Option
    **/
    public function get($code) {
        // TODO: DRY with SD_Currency_DbStore::get
        if (SD_Currency_Config::getByCode($code)->isDefault()) {
            return new SD_Currency_Option($code, 1, new DateTime());
        }
        if (!file_exists($this->filename)) {
            return null;
        }
        $contents = file_get_contents($this->filename);
        $decode = json_decode($contents);
        $data = $decode->$code;
        if (!isset($data->rate) || !isset($data->updateTime)) {
            return null;
        }
        // TODO: DRY with SD_Currency_DbStore::get
        return new SD_Currency_Option($code, $data->rate, new DateTime($data->updateTime));
    }

    /**
     * @param $code     string
     * @param $rate     float
     * @param $datetime DateTime
    **/
    public function set($code, $rate, DateTime $datetime) {
        if (file_exists($this->filename)) {
            $contents = file_get_contents($this->filename);
            $currencies = json_decode($contents);
        } else {
            $currencies = new stdClass();
        }
        $currencies->$code = new SD_Currency_Option($code, $rate, $datetime);
        $encode = json_encode($currencies);
        file_put_contents($this->filename, $encode);
    }
}
