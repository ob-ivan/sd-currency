<?php

namespace SD\Currency\Service;

class Updater {
    const CBR_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';
    const CBR_XPATH = '/ValCurs/Valute[CharCode="$code"]/Value';

    private $store;

    public function __construct(SD_Currency_Service_Store_Interface $store) {
        $this->store = $store;
    }

    public function updateRates() {
        $updateCodes = [];
        foreach (SD_Currency_Config::all() as $config) {
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
                $this->store->set($code, $rate, new DateTime());
            }
        }
    }

    private function needUpdate(SD_Currency_Config $config) {
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
