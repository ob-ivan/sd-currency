<?php
namespace SD\Currency\Service;

use SD\Currency\Model\Currency;
use SD\Currency\Model\Money;
use SD\Currency\Model\Registry;

class Formatter
{
    const CONFIG_KEY_THOUSAND_SEPARATOR = 'thousandSeparator';
    const CONFIG_KEY_SYMBOL_SEPARATOR = 'symbolSeparator';
    const CONFIG_KEY_SYMBOL_TYPE = 'symbolType';
    const CONFIG_KEY_SYMBOL_MAP = 'symbolMap';
    const CONFIG_KEY_ROUND_DIRECTION = 'roundDirection';
    const CONFIG_KEY_ROUND_DIGITS = 'roundDigits';

    const CONFIG_SYMBOL_TYPE_HTML = 'html';
    const CONFIG_SYMBOL_TYPE_UNICODE = 'unicode';
    const CONFIG_SYMBOL_TYPE_FONT_AWESOME = 'fontAwesome';
    const CONFIG_SYMBOL_TYPE_MAP = 'map';
    const CONFIG_SYMBOL_TYPE_NONE = 'none';

    const CONFIG_ROUND_DIRECTION_NONE  = 'none';
    const CONFIG_ROUND_DIRECTION_CEIL  = 'ceil';
    const CONFIG_ROUND_DIRECTION_ROUND = 'round';
    const CONFIG_ROUND_DIRECTION_FLOOR = 'floor';

    const CONFIG_DEFAULT_THOUSAND_SEPARATOR = '&thinsp;';
    const CONFIG_DEFAULT_SYMBOL_SEPARATOR = '&nbsp;';
    const CONFIG_DEFAULT_SYMBOL_TYPE = 'unicode';
    const CONFIG_DEFAULT_ROUND_DIRECTION = 'none';
    const CONFIG_DEFAULT_ROUND_DIGITS = 3;

    private $registry;
    private $config = [];

    public function __construct(Registry $registry, array $config = [])
    {
        $this->registry = $registry;
        $this->config = $config + [
            self::CONFIG_KEY_THOUSAND_SEPARATOR => self::CONFIG_DEFAULT_THOUSAND_SEPARATOR,
            self::CONFIG_KEY_SYMBOL_SEPARATOR   => self::CONFIG_DEFAULT_SYMBOL_SEPARATOR,
            self::CONFIG_KEY_SYMBOL_TYPE        => self::CONFIG_DEFAULT_SYMBOL_TYPE,
            self::CONFIG_KEY_ROUND_DIRECTION    => self::CONFIG_DEFAULT_ROUND_DIRECTION,
            self::CONFIG_KEY_ROUND_DIGITS       => self::CONFIG_DEFAULT_ROUND_DIGITS,
        ];
    }

    public function formatMoney(Money $money): string
    {
        $amount = $this->round($money->getAmount());
        $formatted = number_format($amount, 0, '.', $this->config[self::CONFIG_KEY_THOUSAND_SEPARATOR]);
        if ($this->config[self::CONFIG_KEY_SYMBOL_TYPE] === self::CONFIG_SYMBOL_TYPE_NONE) {
            return $formatted;
        }
        $symbol = $this->getSymbol($money->getCurrency());
        $parts = $money->getCurrency()->getPosition() === Currency::POSITION_AFTER
            ? [$formatted, $symbol]
            : [$symbol, $formatted];
        return implode($parts, $this->config[self::CONFIG_KEY_SYMBOL_SEPARATOR]);
    }

    public function getFontAwesome($symbol)
    {
        $currency = $this->registry->getByHtml($symbol);
        if ($currency) {
            return $this->getFontAwesomeByCurrency($currency);
        }
        return '';
    }

    private function round(int $amount): int
    {
        if (!$amount) {
            return 0;
        }
        $multiplier = 10 ** floor(log10($amount) - $this->config[self::CONFIG_KEY_ROUND_DIGITS] + 1);
        $a = $amount / $multiplier;
        switch ($this->config[self::CONFIG_KEY_ROUND_DIRECTION]) {
            case self::CONFIG_ROUND_DIRECTION_CEIL:  $a = ceil($a);
            case self::CONFIG_ROUND_DIRECTION_ROUND: $a = round($a);
            case self::CONFIG_ROUND_DIRECTION_FLOOR: $a = floor($a);
        }
        return round($a * $multiplier);
    }

    private function getSymbol(Currency $currency): string
    {
        switch ($this->config[self::CONFIG_KEY_SYMBOL_TYPE]) {
            case self::CONFIG_SYMBOL_TYPE_HTML:         return $currency->getHtml();
            case self::CONFIG_SYMBOL_TYPE_UNICODE:      return $currency->getUnicode();
            case self::CONFIG_SYMBOL_TYPE_FONT_AWESOME: return $this->getFontAwesomeByCurrency($currency);
            case self::CONFIG_SYMBOL_TYPE_MAP: return $this->config[self::CONFIG_KEY_SYMBOL_MAP][$currency->getCode()];
        }
    }

    private function getFontAwesomeByCurrency(Currency $currency): string
    {
        return $this->getFontAwesomeByCode($currency->getCode());
    }

    private function getFontAwesomeByCode(string $code): string
    {
        return '<i class="fa fa-' . strtolower($code) . '" aria-hidden="true"></i>';
    }
}
