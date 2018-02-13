<?php
namespace tests\DependencyInjection;

class SubclassConsumer extends ParentConsumer
{
    public function getService()
    {
        return $this->getCurrency();
    }
}
