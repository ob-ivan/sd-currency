<?php
namespace SD\Currency\Service;

use SD\Currency\Model\Currency;
use SD\Currency\Model\Registry;
use SD\Currency\Store\StoreInterface;

class Updater
{
    const CONFIG_KEY_URL = 'url';
    const CONFIG_KEY_XPATH = 'xpath';
    const CONFIG_KEY_UPDATE_INTERVAL = 'updateInterval';

    const CONFIG_DEFAULT_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';
    const CONFIG_DEFAULT_XPATH = '/ValCurs/Valute[CharCode="$code"]/Value';
    const CONFIG_DEFAULT_UPDATE_INTERVAL = '1 day';

    const DEFAULT_CURRENCY_CODE = 'RUB';

    private $store;
    private $registry;
    protected $config;

    public function __construct(Registry $registry, StoreInterface $store, array $config = [])
    {
        $this->store = $store;
        $this->registry = $registry;
        $this->config = $config + [
            self::CONFIG_KEY_URL => self::CONFIG_DEFAULT_URL,
            self::CONFIG_KEY_XPATH => self::CONFIG_DEFAULT_XPATH,
            self::CONFIG_KEY_UPDATE_INTERVAL => self::CONFIG_DEFAULT_UPDATE_INTERVAL,
        ];
    }

    public function updateRates()
    {
        $updateCodes = [];
        foreach ($this->registry->getAll() as $currency) {
            if ($this->needUpdate($currency)) {
                $updateCodes[] = $currency->getCode();
            }
        }
        if ($updateCodes) {
            $xml = file_get_contents($this->config[self::CONFIG_KEY_URL]);
            $simple = new \SimpleXmlElement($xml);
            foreach ($updateCodes as $code) {
                if ($code === self::DEFAULT_CURRENCY_CODE) {
                    $rate = 1;
                } else {
                    $value = $simple->xpath(
                        str_replace('$code', $code, $this->config[self::CONFIG_KEY_XPATH])
                    )[0];
                    $rate = floatval(str_replace(',', '.', $value));
                }
                $this->store->set($code, $rate, new \DateTime());
            }
        }
    }

    private function needUpdate(Currency $currency)
    {
        $currency = $this->store->get($currency->getCode());
        if (!$currency) {
            return true;
        }
        return $currency->getUpdateTime() < new \DateTime("-{$this->config[self::CONFIG_KEY_UPDATE_INTERVAL]}");
    }
}
