<?php
namespace SD\Currency\Service;

use SD\Currency\Model\Money;
use SD\Currency\Model\Registry;
use SD\Currency\Store\StoreInterface;

class Converter {
    private $registry;
    private $store;

    public function __construct(Registry $registry, StoreInterface $store) {
        $this->registry = $registry;
        $this->store = $store;
    }

    public function convert(Money $money, $currency): Money {
        if (is_string($currency)) {
            $currency = $this->registry->getByCode($currency);
        }
        if ($money->getCurrency()->getCode() === $currency->getCode()) {
            return $money;
        }
        $fromRate = $this->store->get($money->getCurrency()->getCode())->getRate();
        $toRate = $this->store->get($currency->getCode())->getRate();
        return new Money($money->getAmount() * $fromRate / $toRate, $currency);
    }
}
