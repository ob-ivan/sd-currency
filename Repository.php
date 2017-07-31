<?php

namespace SD\Currency;

use SD\Currency\Service\Formatter;
use SD\DependencyInjection\DeclarerInterface;
use SD\DependencyInjection\ContainerAwareTrait;

class Repository implements DeclarerInterface {
    use ContainerAwareTrait;

    private $store;

    public function declareDependencies() {
        return ['container'];
    }

    public function option($code, $rate, $datetime) {
        return new SD_Currency_Option($code, $rate, $datetime);
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
            function (SD_Currency_Model_Config $config) use ($shortLabels, $longLabels, $store) {
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
        return SD_Currency_Model_Config::all();
    }

    public function getUpdater() {
        return new Updater($this->getStore());
    }

    public function setStore(SD_Currency_Service_Store_Interface $store) {
        $this->store = $store;
    }

    public function getStore(): SD_Currency_Service_Store_Interface {
        if (!$this->store) {
            $this->store = $this->container->produce(SD_Currency_Service_Store_File::class);
        }
        return $this->store;
        // return $this->container->produce(SD_Currency_Service_Store_Db::class);
    }

    public function getFormatter() {
        return new Formatter();
    }

    public function getConfigByCode(string $code) {
        return SD_Currency_Model_Config::getByCode($code);
    }

    public function getConfigBySymbol(string $symbol) {
        return SD_Currency_Model_Config::getBySymbol($symbol);
    }
}
