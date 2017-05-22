<?php

class SD_Currency_Repository implements SD_DI_DeclarerInterface {
    use SD_DI_ContainerAwareTrait;

    public function declareDependencies() {
        return ['container'];
    }

    public function getAllConfigs() {
        return SD_Currency_Model_Config::all();
    }

    public function getConfigBySymbol(string $symbol) {
        return SD_Currency_Model_Config::getBySymbol($symbol);
    }

    public function getStore() {
        return $this->container->produce(SD_Currency_Service_Store_Db::class);
    }
}
