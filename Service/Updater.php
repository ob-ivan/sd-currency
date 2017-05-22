<?php

class SD_Currency_Service_Updater {
    private $store;

    public function __construct() {
        $this->store = SD_Currency_StoreFactory::create();
    }

    public function updateRates() {
        $updateCodes = [];
        foreach (SD_Currency_Config::all() as $config) {
            if ($this->needUpdate($config)) {
                $updateCodes[] = $config->getCode();
            }
        }
        if ($updateCodes) {
            $xml = file_get_contents('http://www.cbr.ru/scripts/XML_daily.asp');
            $simple = new SimpleXmlElement($xml);
            foreach ($updateCodes as $code) {
                $value = $simple->xpath("/ValCurs/Valute[CharCode='$code']/Value")[0];
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
        return $currency->getUpdateTime() < new DateTime('-1 day');
    }
}
