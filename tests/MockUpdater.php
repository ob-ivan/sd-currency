<?php
namespace tests;

use SD\Currency\Service\Updater;

class MockUpdater extends Updater
{
    public function getConfig()
    {
        return $this->config;
    }
}
