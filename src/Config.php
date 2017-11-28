<?php
namespace SD\Currency;

class Config {
    private $code;
    private $symbol;
    private $isDefault;

    private function __construct(string $code, string $symbol, bool $isDefault) {
        $this->code = $code;
        $this->symbol = $symbol;
        $this->isDefault = $isDefault;
    }

    public static function all() {
        return [
            self::getByCode('RUB'),
            self::getByCode('USD'),
            self::getByCode('EUR'),
        ];
    }

    public static function getByCode(string $code) {
        switch ($code) {
            case 'RUB': return new self('RUB', '&#8381;', true);  // ₽
            case 'USD': return new self('USD', '&#36;',   false); // $
            case 'EUR': return new self('EUR', '&#8364;', false); // €
        }
    }

    public static function getBySymbol(string $symbol) {
        $filter = array_filter(
            self::all(),
            function (self $config) use ($symbol) {
                return $config->symbol === $symbol;
            }
        );
        return array_shift($filter);
    }

    public function getCode() {
        return $this->code;
    }

    public function getSymbol() {
        return $this->symbol;
    }

    public function isDefault() {
        return $this->isDefault;
    }
}
