<?php
namespace SD\Currency\Service;

use SD\Currency\Model\Currency;
use SD\Currency\Model\Registry;
use SD\Currency\Store\StoreInterface;

class Updater {
    const CBR_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';
    const CBR_XPATH = '/ValCurs/Valute[CharCode="$code"]/Value';
    const DEFAULT_CURRENCY_CODE = 'RUB';
    const UPDATE_INTERVAL = '1 day';

    private $store;
    private $registry;
    private $config;

    public function __construct(StoreInterface $store, Registry $registry, array $config = []) {
        $this->store = $store;
        $this->registry = $registry;
        $this->config = $config + [
            'url' => self::CBR_URL,
            'xpath' => self::CBR_XPATH,
            'update_interval' => self::UPDATE_INTERVAL,
        ];
    }

    public function updateRates() {
        $updateCodes = [];
        foreach ($this->registry->getAll() as $currency) {
            if ($this->needUpdate($currency)) {
                $updateCodes[] = $currency->getCode();
            }
        }
        if ($updateCodes) {
            $xml = file_get_contents($this->config['url']);
            $simple = new \SimpleXmlElement($xml);
            foreach ($updateCodes as $code) {
                if ($code === self::DEFAULT_CURRENCY_CODE) {
                    $rate = 1;
                } else {
                    $value = $simple->xpath(
                        str_replace('$code', $code, $this->config['xpath'])
                    )[0];
                    $rate = floatval(str_replace(',', '.', $value));
                }
                $this->store->set($code, $rate, new \DateTime());
            }
        }
    }

    private function needUpdate(Currency $currency) {
        $currency = $this->store->get($currency->getCode());
        if (!$currency) {
            return true;
        }
        return $currency->getUpdateTime() < new \DateTime("-{$this->config['update_interval']}");
    }
}
