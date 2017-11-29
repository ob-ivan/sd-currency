<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use SD\Currency\Model\Money;
use SD\Currency\Model\Registry;
use SD\Currency\Service\Formatter;

class FormatterTest extends TestCase {
    /**
     * @dataProvider formatPriceDataProvider
    **/
    public function testFormatPrice($config, $price, $symbol, $expected, $message) {
        $registry = new Registry();
        $formatter = new Formatter($registry, $config);
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
            [
                'config' => [
                    'fontAwesome' => false,
                ],
                'price' => '271828',
                'symbol' => '&#8364;',
                'expected' => '&#8364;&nbsp;271&thinsp;828',
                'message' => 'Must disable font awesome if requested by config',
            ],
        ];
    }

    /**
     * @dataProvider formatMoneyDataProvider
    **/
    public function testFormatMoney($config, $money, $expected, $message) {
        $registry = new Registry();
        $formatter = new Formatter($registry, $config);
        $this->assertEquals($expected, $formatter->formatMoney($money), $message);
    }

    public function formatMoneyDataProvider() {
        $registry = new Registry();
        return [
            [
                'config' => [
                    'thousandSeparator' => '+',
                    'symbolType' => 'html',
                ],
                'money' => new Money(7777777, $registry->getByCode('RUB')),
                'expected' => '7+777+777&nbsp;&#8381;',
                'message' => 'Must place RUB html symbol after the number',
            ],
            [
                'config' => [
                    'thousandSeparator' => '/',
                    'symbolType' => 'unicode',
                ],
                'money' => new Money(123456, $registry->getByCode('USD')),
                'expected' => '$&nbsp;123/456',
                'message' => 'Must place USD unicode symbol before the number',
            ],
            [
                'config' => [
                    'symbolType' => 'fontAwesome',
                ],
                'money' => new Money(31415926, $registry->getByCode('EUR')),
                'expected' => '<i class="fa fa-eur" aria-hidden="true"></i>&nbsp;31&thinsp;415&thinsp;926',
                'message' => 'Must insert font awesome tag',
            ],
            [
                'config' => [
                    'symbolType' => 'map',
                    'symbolMap' => [
                        'RUB' => 'руб.',
                    ],
                ],
                'money' => new Money(271828, $registry->getByCode('RUB')),
                'expected' => '271&thinsp;828&nbsp;руб.',
                'message' => 'Must get currency symbol from a map',
            ],
        ];
    }
}
