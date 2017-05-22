<?php

class SD_Currency_Repository implements SD_DI_DeclarerInterface {
    use SD_DI_ContainerAwareTrait;

    public function getAllConfigs() {
        return SD_Currency_Model_Config::all();
    }

    public function getStore() {
        return $this->container->produce(SD_Currency_Service_Store_Db::class);
    }
}
