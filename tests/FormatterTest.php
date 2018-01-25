<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use SD\Currency\Model\Money;
use SD\Currency\Model\Registry;
use SD\Currency\Service\Formatter;

class FormatterTest extends TestCase {
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
            [
                'config' => [
                    'thousandSeparator' => '&nbsp;',
                    'roundDirection' => 'round',
                    'roundDigits' => 3,
                ],
                'money' => new Money(9253377, $registry->getByCode('USD')),
                'expected' => '$&nbsp;9&nbsp;250&nbsp;000',
                'message' => 'Must round amount to given number of significant digits',
            ],
            [
                'config' => [
                    'thousandSeparator' => "'",
                    'symbolType' => 'none',
                ],
                'money' => new Money(20170112, $registry->getByCode('RUB')),
                'expected' => "20'170'112",
                'message' => 'Must hide currency symbol',
            ],
            [
                'config' => [
                    'symbolSeparator' => '',
                ],
                'money' => new Money(0, $registry->getByCode('USD')),
                'expected' => '$0',
                'message' => 'Must format zero value',
            ],
        ];
    }
}
