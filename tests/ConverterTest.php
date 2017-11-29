<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use SD\Currency\Model\Money;
use SD\Currency\Model\Registry;
use SD\Currency\Service\Converter;
use SD\Currency\Store\ArrayStore;
use SD\Currency\Store\Record;

class ConverterTest extends TestCase {
    private $registry;
    private $store;

    public function setUp() {
        $this->registry = new Registry();
        $this->store = new ArrayStore([
            new Record('RUB',  1, new \DateTime()),
            new Record('USD', 65, new \DateTime()),
            new Record('EUR', 75, new \DateTime()),
        ]);
    }

    /**
     * @dataProvider convertDataProvider
    **/
    public function testConvert($money, $currency, $expectedAmount, $expectedCurrency, $description) {
        $converter = new Converter($this->registry, $this->store);
        $converted = $converter->convert($money, $currency);
        $this->assertInstanceOf(Money::class, $converted, "Converted money must be an instance of money class ($description)");
        $this->assertEquals($expectedAmount, $converted->getAmount(), "Converted amount must match ($description)");
        $this->assertEquals($expectedCurrency, $converted->getCurrency(), "Converted currency must match ($description)");
    }

    public function convertDataProvider() {
        $registry = new Registry();
        return [
            [
                'money' => new Money(6500, $registry->getByCode('RUB')),
                'currency' => $registry->getByCode('USD'),
                'expectedAmount' => 100,
                'expectedCurrency' => $registry->getByCode('USD'),
                'description' => 'RUB to USD',
            ],
            [
                'money' => new Money(15000, $registry->getByCode('USD')),
                'currency' => 'EUR',
                'expectedAmount' => 13000,
                'expectedCurrency' => $registry->getByCode('EUR'),
                'description' => 'USD to EUR',
            ],
            [
                'money' => new Money(2000000, $registry->getByCode('EUR')),
                'currency' => 'RUB',
                'expectedAmount' => 150000000,
                'expectedCurrency' => $registry->getByCode('RUB'),
                'description' => 'EUR to RUB',
            ],
            [
                'money' => new Money(3000000000, $registry->getByCode('RUB')),
                'currency' => 'RUB',
                'expectedAmount' => 3000000000,
                'expectedCurrency' => $registry->getByCode('RUB'),
                'description' => 'RUB to RUB',
            ],
        ];
    }
}
