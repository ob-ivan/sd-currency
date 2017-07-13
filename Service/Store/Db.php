<?php

class SD_Currency_Service_Store_Db implements SD_Currency_Service_Store_Interface, SD_DI_DeclarerInterface {
    use SD_CoreService_Wordpress_AwareTrait;

    private $TABLENAME = 'wpcurr';

    public function declareDependencies() {
        return ['wordpress'];
    }

    public function get(string $code): SD_Currency_Model_Option {
        // TODO: DRY with SD_Currency_FileStore::get
        if (SD_Currency_Model_Config::getByCode($code)->isDefault()) {
            return new SD_Currency_Model_Option($code, 1, new DateTime());
        }
        $rows = $this->wpdb()->get_results($this->wpdb()->prepare(
            "
                SELECT rate, update_time
                FROM {$this->TABLENAME}
                WHERE code = %s
            ",
            $code
        ));
        if (!$rows) {
            return null;
        }
        $row = $rows[0];
        // TODO: DRY with SD_Currency_FileStore::get
        return new SD_Currency_Model_Option($code, $row->rate, new DateTime($row->update_time));
    }

    public function set(string $code, float $rate, DateTime $datetime) {
        $this->wpdb()->query($this->wpdb()->prepare(
            "
                REPLACE INTO {$this->TABLENAME} (
                    code,
                    rate,
                    update_time
                ) VALUES (
                    %s,
                    %s,
                    NOW()
                )
            ",
            $code,
            $rate,
            $datetime->format('Y-m-d H:i:s')
        ));
    }

    private function wpdb() {
        return $this->wordpress->wpdb();
    }
}
