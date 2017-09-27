<?php

namespace SD\Currency\Service;

use SD\Currency\Config;

class Formatter {
    const CONFIG_KEY_SEPARATOR = 'separator';
    const CONFIG_KEY_FONT_AWESOME = 'fontAwesome';

    const CONFIG_DEFAULT_SEPARATOR = '&thinsp;';
    const CONFIG_DEFAULT_FONT_AWESOME = true;

    private $config = [];

    public function __construct(array $config = []) {
        $this->config = $config + [
            self::CONFIG_KEY_SEPARATOR    => self::CONFIG_DEFAULT_SEPARATOR,
            self::CONFIG_KEY_FONT_AWESOME => self::CONFIG_DEFAULT_FONT_AWESOME,
        ];
    }

    public function formatPrice(string $price, string $symbol): string {
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
            $fontAwesome = $this->getFontAwesome($symbol);
            $parts = [];
            if ($format === 'PRICE_SYMBOL') {
                $parts = [$formatted, $fontAwesome];
            } elseif ($format === 'SYMBOL_PRICE') {
                $parts = [$fontAwesome, $formatted];
            }
            $output = implode($parts, '&nbsp;');
        }
        return $output;
    }

    public function getFontAwesome($symbol) {
        $config = Config::getBySymbol($symbol);
        if ($config) {
            return '<i class="fa fa-' . strtolower($config->getCode()) . '" aria-hidden="true"></i>';
        }
        return '';
    }
}
