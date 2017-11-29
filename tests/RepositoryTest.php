<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use SD\Currency\Model\Money;
use SD\Currency\Model\Registry;
use SD\Currency\Repository;
use SD\Currency\Service\Converter;
use SD\Currency\Store\ArrayStore;
use SD\Currency\Store\FileStore;

class RepositoryTest extends TestCase {
    /**
     * @dataProvider createMoneyDataProvider
    **/
    public function testCreateMoney($amount, $currency, $expectedCurrency) {
        $repository = new Repository();
        $money = $repository->createMoney($amount, $currency);
        $this->assertInstanceOf(Money::class, $money, 'Created money must be of money class');
        $this->assertEquals($amount, $money->getAmount(), 'Created amount must match');
        $this->assertEquals($expectedCurrency, $money->getCurrency(), 'Created currency must match');
    }

    public function createMoneyDataProvider() {
        $registry = new Registry();
        return [
            [
                'amount' => 450,
                'currency' => $registry->getByCode('RUB'),
                'expectedCurrency' => $registry->getByCode('RUB'),
                'description' => 'Currency as object',
            ],
            [
                'amount' => 550,
                'currency' => 'USD',
                'expectedCurrency' => $registry->getByCode('USD'),
                'description' => 'Currency as string',
            ],
        ];
    }

    public function testGetOptions() {
        $repository = new Repository();
        $repository->setStore(new FileStore(__DIR__));
        $options = $repository->getOptions();
        $this->assertInternalType('array', $options, 'Options must be an array');
        $this->assertGreaterThanOrEqual(3, count($options), 'Must return at least 3 options');
        foreach ($options as $option) {
            foreach ([
                'code' => 'string',
                'symbol' => 'string',
                'rate' => 'float',
            ] as $field => $type) {
                $this->assertInternalType('object', $option, "Option must be an object");
                $this->assertObjectHasAttribute($field, $option, "Option must contain $field field");
                $this->assertInternalType($type, $option->$field, "Field $field must have type $type");
            }
        }
    }

    public function testGetConverter() {
        $repository = new Repository();
        $repository->setStore(new ArrayStore([]));
        $converter = $repository->getConverter();
        $this->assertInstanceOf(Converter::class, $converter, 'Must return instance of converter');
    }
}
