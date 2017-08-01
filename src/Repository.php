<?php

namespace SD\Currency;

use SD\Currency\Service\Formatter;
use SD\Currency\Service\Updater;
use SD\Currency\Store\FileStore;
use SD\Currency\Store\Record;
use SD\Currency\Store\StoreInterface;
use SD\DependencyInjection\DeclarerInterface;
use SD\DependencyInjection\ContainerAwareTrait;

class Repository implements DeclarerInterface {
    use ContainerAwareTrait;

    private $store;

    public function declareDependencies() {
        return ['container'];
    }

    public function createRecord($code, $rate, $datetime) {
        return new Record($code, $rate, $datetime);
    }

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
                return (object)[
                    'code' => $code,
                    'symbol' => $config->getSymbol(),
                    'shortLabel' => $shortLabels[$code],
                    'longLabel' => $longLabels[$code],
                    'rate' => $store->get($code)->getRate(),
                ];
            },
            $this->getAllConfigs()
        );
    }

    public function getAllConfigs() {
        return Config::all();
    }

    public function getUpdater() {
        return new Updater($this->getStore());
    }

    public function setStore(StoreInterface $store) {
        $this->store = $store;
    }

    public function getStore(): StoreInterface {
        if (!$this->store) {
            $this->store = $this->container->produce(FileStore::class);
        }
        return $this->store;
        // return $this->container->produce(SD_Currency_Service_Store_Db::class);
    }

    public function getFormatter() {
        return new Formatter();
    }

    public function getConfigByCode(string $code) {
        return Config::getByCode($code);
    }

    public function getConfigBySymbol(string $symbol) {
        return Config::getBySymbol($symbol);
    }
}
