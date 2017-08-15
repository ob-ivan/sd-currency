<?php

namespace SD\Currency;

use SD\Currency\Service\Formatter;
use SD\Currency\Service\Updater;
use SD\Currency\Store\FileStore;
use SD\Currency\Store\Record;
use SD\Currency\Store\StoreInterface;

class Repository {
    /** @var StoreInterface */
    private $store = null;

    public function getOptions() {
        $shortLabels = [
            'RUB' => 'рублях',
            'USD' => 'долларах',
            'EUR' => 'евро',
        ];
        $longLabels = [
            'RUB' => 'стоимость в рублях',
            'USD' => 'стоимость в долларах',
            'EUR' => 'стоимость в евро',
        ];
        $store = $this->getStore();
        return array_map(
            function (Config $config) use ($shortLabels, $longLabels, $store) {
                $code = $config->getCode();
                $record = $store->get($code);
                return (object)[
                    'code' => $code,
                    'symbol' => $config->getSymbol(),
                    'shortLabel' => $shortLabels[$code],
                    'longLabel' => $longLabels[$code],
                    'rate' => floatval($record ? $record->getRate() : 0),
                ];
            },
            $this->getAllConfigs()
        );
    }

    public function getUpdater(array $config = []) {
        return new Updater($this->getStore(), $config);
    }

    public function setStore(StoreInterface $store) {
        $this->store = $store;
    }

    public function getStore(): ?StoreInterface {
        return $this->store;
    }

    public function createRecord($code, $rate, $datetime) {
        return new Record($code, $rate, $datetime);
    }

    public function getFormatter(array $config = []) {
        return new Formatter($config);
    }

    public function getAllConfigs() {
        return Config::all();
    }

    public function getConfigByCode(string $code) {
        return Config::getByCode($code);
    }

    public function getConfigBySymbol(string $symbol) {
        return Config::getBySymbol($symbol);
    }
}
