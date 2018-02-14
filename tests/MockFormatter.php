<?php
namespace tests;

use SD\Currency\Service\Formatter;

class MockFormatter extends Formatter
{
    public function getConfig()
    {
        return $this->config;
    }
}
