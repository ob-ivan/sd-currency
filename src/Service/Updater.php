<?php

namespace SD\Currency\Service;

use SD\Currency\Config;
use SD\Currency\Store\StoreInterface;

class Updater {
    const CBR_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';
    const CBR_XPATH = '/ValCurs/Valute[CharCode="$code"]/Value';
    const UPDATE_INTERVAL = '1 day';

    private $store;
    private $config;

    public function __construct(StoreInterface $store, array $config = []) {
        $this->store = $store;
        $this->config = $config + [
            'url' => self::CBR_URL,
            'xpath' => self::CBR_XPATH,
            'update_interval' => self::UPDATE_INTERVAL,
        ];
    }

    public function updateRates() {
        $updateCodes = [];
        foreach (Config::all() as $config) {
            if ($this->needUpdate($config)) {
                $updateCodes[] = $config->getCode();
            }
        }
        if ($updateCodes) {
            $xml = file_get_contents($this->config['url']);
            $simple = new \SimpleXmlElement($xml);
            foreach ($updateCodes as $code) {
                $value = $simple->xpath(
                    str_replace('$code', $code, $this->config['xpath'])
                )[0];
                $rate = floatval(str_replace(',', '.', $value));
                $this->store->set($code, $rate, new \DateTime());
            }
        }
    }

    private function needUpdate(Config $config) {
        if ($config->isDefault()) {
            return false;
        }
        $currency = $this->store->get($config->getCode());
        if (!$currency) {
            return true;
        }
        return $currency->getUpdateTime() < new \DateTime("-{$this->config['update_interval']}");
    }
}
