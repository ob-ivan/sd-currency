<?php
namespace SD\Currency\Service;

use SD\Currency\Model\Currency;
use SD\Currency\Model\Money;
use SD\Currency\Model\Registry;

class Formatter {
    // NEW //
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
    // OLD //
    const CONFIG_KEY_SEPARATOR = 'separator';
    const CONFIG_KEY_FONT_AWESOME = 'fontAwesome';

    const CONFIG_DEFAULT_SEPARATOR = '&thinsp;';
    const CONFIG_DEFAULT_FONT_AWESOME = true;
    // END //

    private $registry;
    private $config = [];

    public function __construct(Registry $registry, array $config = []) {
        $this->registry = $registry;
        if (isset($config[self::CONFIG_KEY_SEPARATOR])) {
            trigger_error('Use thousandSeparator instead', E_USER_DEPRECATED);
        }
        if (isset($config[self::CONFIG_KEY_FONT_AWESOME])) {
            trigger_error('Use symbolType instead', E_USER_DEPRECATED);
        }
        $this->config = $config + [
            // NEW //
            self::CONFIG_KEY_THOUSAND_SEPARATOR => self::CONFIG_DEFAULT_THOUSAND_SEPARATOR,
            self::CONFIG_KEY_SYMBOL_SEPARATOR   => self::CONFIG_DEFAULT_SYMBOL_SEPARATOR,
            self::CONFIG_KEY_SYMBOL_TYPE        => self::CONFIG_DEFAULT_SYMBOL_TYPE,
            self::CONFIG_KEY_ROUND_DIRECTION    => self::CONFIG_DEFAULT_ROUND_DIRECTION,
            self::CONFIG_KEY_ROUND_DIGITS       => self::CONFIG_DEFAULT_ROUND_DIGITS,
            // OLD //
            self::CONFIG_KEY_SEPARATOR    => self::CONFIG_DEFAULT_SEPARATOR,
            self::CONFIG_KEY_FONT_AWESOME => self::CONFIG_DEFAULT_FONT_AWESOME,
            // END //
        ];
    }

    public function formatMoney(Money $money): string {
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

    public function formatPrice(string $price, string $symbol): string {
        trigger_error('Use formatMoney instead', E_USER_DEPRECATED);
        if ($symbol === '&#8381;') { // rub
            $format = 'PRICE_SYMBOL';
        } elseif (in_array($symbol, array(
            '&#36;',    // usd
            '&#8364;',  // euro
            '&euro;',
        ))) {
            $format = 'SYMBOL_PRICE';
        } else {
            // Цена по запросу
            $format = 'PRICE';
        }
        if ($format === 'PRICE') {
            $output = $price;
        } else {
            $separator = $this->config[self::CONFIG_KEY_SEPARATOR];
            $formatted = preg_replace(
                '/(?<=\d)&(?=\d)/',
                $separator,
                number_format((int)$price, 0, '.', $separator)
            );
            if ($this->config[self::CONFIG_KEY_FONT_AWESOME]) {
                $symbol = $this->getFontAwesome($symbol);
            }
            $parts = [];
            if ($format === 'PRICE_SYMBOL') {
                $parts = [$formatted, $symbol];
            } elseif ($format === 'SYMBOL_PRICE') {
                $parts = [$symbol, $formatted];
            }
            $output = implode($parts, '&nbsp;');
        }
        return $output;
    }

    public function getFontAwesome($symbol) {
        $currency = $this->registry->getByHtml($symbol);
        if ($currency) {
            return $this->getFontAwesomeByCurrency($currency);
        }
        return '';
    }

    private function round(int $amount): int {
        $multiplier = 10 ** floor(log10($amount) - $this->config[self::CONFIG_KEY_ROUND_DIGITS] + 1);
        $a = $amount / $multiplier;
        switch ($this->config[self::CONFIG_KEY_ROUND_DIRECTION]) {
            case self::CONFIG_ROUND_DIRECTION_CEIL:  $a = ceil($a);
            case self::CONFIG_ROUND_DIRECTION_ROUND: $a = round($a);
            case self::CONFIG_ROUND_DIRECTION_FLOOR: $a = floor($a);
        }
        return round($a * $multiplier);
    }

    private function getSymbol(Currency $currency): string {
        switch ($this->config[self::CONFIG_KEY_SYMBOL_TYPE]) {
            case self::CONFIG_SYMBOL_TYPE_HTML:         return $currency->getHtml();
            case self::CONFIG_SYMBOL_TYPE_UNICODE:      return $currency->getUnicode();
            case self::CONFIG_SYMBOL_TYPE_FONT_AWESOME: return $this->getFontAwesomeByCurrency($currency);
            case self::CONFIG_SYMBOL_TYPE_MAP: return $this->config[self::CONFIG_KEY_SYMBOL_MAP][$currency->getCode()];
        }
    }

    private function getFontAwesomeByCurrency(Currency $currency): string {
        return $this->getFontAwesomeByCode($currency->getCode());
    }

    private function getFontAwesomeByCode(string $code): string {
        return '<i class="fa fa-' . strtolower($code) . '" aria-hidden="true"></i>';
    }
}
