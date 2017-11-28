<?php
namespace tests\Updater;

use PHPUnit\Framework\TestCase;
use SD\Currency\Service\Updater;
use SD\Currency\Store\Record;

class UpdaterTest extends TestCase {
    public function testUpdateRates() {
        $store = new ArrayStore([
            new Record('USD', 60, new \DateTime('-1 min')),
            new Record('EUR', 70, new \DateTime('-3 min')),
        ]);
        $updater = new Updater($store, [
            'url' => __DIR__ . '/rates.xml',
            'xpath' => '/currencies/currency[code = "$code"]/rate',
            'update_interval' => '2 min',
        ]);
        $updater->updateRates();
        $this->assertEquals(60, $store->get('USD')->getRate(), 'Must not change recently updated USD rate');
        $this->assertEquals(75, $store->get('EUR')->getRate(), 'Must update EUR rate from xml');
    }
}
