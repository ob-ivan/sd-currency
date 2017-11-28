<?php
namespace SD\Currency;

use SD\Currency\Model\Currency;
use SD\Currency\Model\Registry;
use SD\Currency\Service\Formatter;
use SD\Currency\Service\Updater;
use SD\Currency\Store\Record;
use SD\Currency\Store\StoreInterface;

class Repository {
    private $registry;

    /** @var StoreInterface */
    private $store = null;

    public function __construct() {
        $this->registry = new Registry();
    }

    public function getOptions() {
        $store = $this->getStore();
        return array_map(
            function (Currency $currency) use ($store) {
                $code = $currency->getCode();
                $record = $store->get($code);
                return (object)[
                    'code' => $code,
                    'symbol' => $currency->getHtml(),
                    'rate' => floatval($record ? $record->getRate() : 0),
                ];
            },
            $this->registry->getAll()
        );
    }

    public function getUpdater(array $config = []) {
        return new Updater($this->getStore(), $this->registry, $config);
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

    public function getRegistry() {
        return $this->registry;
    }

    public function getAllConfigs() {
        trigger_error(__METHOD__ . ' is deprecated, use ' . __CLASS__ . '->getRegistry()->getAll() instead');
        return Config::all();
    }

    public function getConfigBySymbol(string $symbol) {
        trigger_error(__METHOD__ . ' is deprecated, use ' . __CLASS__ . '->getRegistry()->getByHtml() instead');
        return Config::getBySymbol($symbol);
    }
}
