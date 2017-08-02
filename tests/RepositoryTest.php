<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use SD\Currency\Repository;
use SD\Currency\Store\FileStore;

class RepositoryTest extends TestCase {
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
                'shortLabel' => 'string',
                'longLabel' => 'string',
                'rate' => 'float',
            ] as $field => $type) {
                $this->assertInternalType('object', $option, "Option must be an object");
                $this->assertObjectHasAttribute($field, $option, "Option must contain $field field");
                $this->assertInternalType($type, $option->$field, "Field $field must have type $type");
            }
        }
    }
}
