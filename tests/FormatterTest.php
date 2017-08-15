<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use SD\Currency\Service\Formatter;

class FormatterTest extends TestCase {
    /**
     * @dataProvider formatPriceDataProvider
    **/
    public function testFormatPrice($config, $price, $symbol, $expected, $message) {
        $formatter = new Formatter($config);
        $this->assertEquals($expected, $formatter->formatPrice($price, $symbol), $message);
    }

    public function formatPriceDataProvider() {
        return [
            [
                'config' => [],
                'price' => 'On demand',
                'symbol' => '',
                'expected' => 'On demand',
                'message' => 'Must not change plain string values',
            ],
            [
                'config' => [
                    'separator' => '+',
                ],
                'price' => '7777777',
                'symbol' => '&#8381;',
                'expected' => '7+777+777&nbsp;<i class="fa fa-rub" aria-hidden="true"></i>',
                'message' => 'Must place RUB symbol after number',
            ],
            [
                'config' => [
                    'separator' => '/',
                ],
                'price' => '123456',
                'symbol' => '&#36;',
                'expected' => '<i class="fa fa-usd" aria-hidden="true"></i>&nbsp;123/456',
                'message' => 'Must place USD symbol before number',
            ],
            [
                'config' => [],
                'price' => '31415926',
                'symbol' => '&#8364;',
                'expected' => '<i class="fa fa-eur" aria-hidden="true"></i>&nbsp;31&thinsp;415&thinsp;926',
                'message' => 'Must insert default separator if not provided in config',
            ],
        ];
    }
}
