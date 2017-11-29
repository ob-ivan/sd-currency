<?php
namespace SD\Currency\Store;

class ArrayStore implements StoreInterface {
    private $records = [];

    public function __construct(array $records) {
        foreach ($records as $record) {
            $this->records[$record->getCode()] = $record;
        }
    }

    public function get(string $code): ?Record {
        return $this->records[$code] ?? null;
    }

    public function set(string $code, float $rate, \DateTime $datetime) {
        $this->records[$code] = new Record($code, $rate, $datetime);
    }
}
