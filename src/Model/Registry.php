<?php
namespace SD\Currency\Model;

class Registry {
    private $currenciesByCode;

    public function __construct() {
        $this->currenciesByCode = [
            'RUB' => new Currency('RUB', '₽', '&#8381;', Currency::POSITION_AFTER),
            'USD' => new Currency('USD', '$', '&#36;', Currency::POSITION_BEFORE),
            'EUR' => new Currency('EUR', '€', '&#8364;', Currency::POSITION_BEFORE),
        ];
    }

    public function getAll() {
        return $this->currenciesByCode;
    }

    public function getByCode(string $code) {
        return $this->currenciesByCode[$code];
    }

    public function getByHtml(string $html) {
        $filter = array_filter(
            $this->currenciesByCode,
            function (Currency $currency) use ($html) {
                return $currency->getHtml() === $html;
            }
        );
        return array_shift($filter);
    }
}
