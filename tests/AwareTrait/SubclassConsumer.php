<?php
namespace tests\AwareTrait;

class SubclassConsumer extends ParentConsumer {
    public function getService() {
        return $this->getCurrency();
    }
}
