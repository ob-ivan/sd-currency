<?php
namespace SD\Currency\Store;

use SD\Currency\Config;

class FileStore implements StoreInterface {
    const FILENAME = 'currencies.json';

    public function __construct(string $dir) {
        $this->filename = $dir . '/' . self::FILENAME;
    }

    /**
     * @param $code string
     * @return SD_Currency_Option
    **/
    public function get(string $code): ?Record {
        /* // TODO: Move to updater.
        if (Config::getByCode($code)->isDefault()) {
            return new Record($code, 1, new \DateTime());
        }
        */
        if (!file_exists($this->filename)) {
            return null;
        }
        $contents = file_get_contents($this->filename);
        $decode = json_decode($contents);
        $data = $decode->$code;
        if (!isset($data->rate) || !isset($data->updateTime)) {
            return null;
        }
        return new Record($code, $data->rate, new \DateTime($data->updateTime));
    }

    /**
     * @param $code     string
     * @param $rate     float
     * @param $datetime DateTime
    **/
    public function set(string $code, float $rate, \DateTime $datetime) {
        if (file_exists($this->filename)) {
            $contents = file_get_contents($this->filename);
            $currencies = json_decode($contents);
        } else {
            $currencies = new \stdClass();
        }
        $currencies->$code = new Record($code, $rate, $datetime);
        $encode = json_encode($currencies);
        file_put_contents($this->filename, $encode);
    }
}
