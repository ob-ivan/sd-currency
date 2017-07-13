<?php

class SD_Currency_Repository implements SD_DI_DeclarerInterface {
    use SD_DI_ContainerAwareTrait;

    public function declareDependencies() {
        return ['container'];
    }

    public function getOptions() {
        $shortLabels = [
            'RUB' => 'рублях',
            'USD' => 'долларах',
            'EUR' => 'евро',
        ];
        $longLabels = [
            'RUB' => 'стоимость в рублях',
            'USD' => 'стоимость в долларах',
            'EUR' => 'стоимость в евро',
        ];
        $store = $this->getStore();
        return array_map(
            function (SD_Currency_Model_Config $config) use ($shortLabels, $longLabels, $store) {
                $code = $config->getCode();
                return (object)[
                    'code' => $code,
                    'symbol' => $config->getSymbol(),
                    'shortLabel' => $shortLabels[$code],
                    'longLabel' => $longLabels[$code],
                    'rate' => $store->get($code)->getRate(),
                ];
            },
            $this->getAllConfigs()
        );
    }

    public function getAllConfigs() {
        return SD_Currency_Model_Config::all();
    }

    public function getStore() {
        return $this->container->produce(SD_Currency_Service_Store_Db::class);
    }

    public function formatPrice($price, $symbol) {
        $price = (int)$price;
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
            $output = esc_attr($price);
        } else {
            $separator = '&thinsp;';
            $formatted = preg_replace(
                '/(?<=\d)&(?=\d)/',
                $separator,
                number_format($price, 0, '.', $separator)
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
        $config = $this->getConfigBySymbol($symbol);
        if ($config) {
            return '<i class="fa fa-' . strtolower($config->getCode()) . '" aria-hidden="true"></i>';
        }
        return '';
    }

    public function getConfigBySymbol(string $symbol) {
        return SD_Currency_Model_Config::getBySymbol($symbol);
    }
}
