<?php

namespace SD\Currency\Service;

use SD\Currency\Model\Config;
use SD\Currency\Store\StoreInterface;

class Updater {
    const CBR_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';
    const CBR_XPATH = '/ValCurs/Valute[CharCode="$code"]/Value';

    private $store;

    public function __construct(StoreInterface $store) {
        $this->store = $store;
    }

    public function updateRates() {
        $updateCodes = [];
        foreach (Config::all() as $config) {
            if ($this->needUpdate($config)) {
                $updateCodes[] = $config->getCode();
            }
        }
        if ($updateCodes) {
            $xml = file_get_contents(self::CBR_URL);
            $simple = new \SimpleXmlElement($xml);
            foreach ($updateCodes as $code) {
                $value = $simple->xpath(
                    str_replace('$code', $code, self::CBR_XPATH)
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
        return $currency->getUpdateTime() < new \DateTime('-1 day');
    }
}
