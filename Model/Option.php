<?php

class SD_Currency_Model_Option implements JsonSerializable {
    /** @var $code string */
    private $code;

    /** @var $rate float */
    private $rate;

    /** @var $udpateTime DateTime */
    private $updateTime;

    public function __construct(string $code, float $rate, DateTime $updateTime) {
        $this->code = $code;
        $this->rate = $rate;
        $this->updateTime = $updateTime;
    }

    public function getRate() {
        return $this->rate;
    }

    public function getUpdateTime() {
        return $this->updateTime;
    }

    public function jsonSerialize() {
        return [
            'oode' => $this->code,
            'rate' => $this->rate,
            'updateTime' => $this->updateTime->format('Y-m-d H:i:s'),
        ];
    }
}
