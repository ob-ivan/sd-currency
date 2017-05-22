<?php

class SD_Currency_Service_Store_Db implements SD_Currency_Service_Store_Interface {
    private $TABLENAME = 'wpcurr';
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * @param $code string
     * @return SD_Currency_Option
    **/
    public function get($code) {
        // TODO: DRY with SD_Currency_FileStore::get
        if (SD_Currency_Config::getByCode($code)->isDefault()) {
            return new SD_Currency_Option($code, 1, new DateTime());
        }
        $rows = $this->wpdb->get_results($this->wpdb->prepare(
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
        return new SD_Currency_Option($code, $row->rate, new DateTime($row->update_time));
    }

    /**
     * @param $code     string
     * @param $rate     float
     * @param $datetime DateTime
    **/
    public function set($code, $rate, DateTime $datetime) {
        $this->wpdb->query($this->wpdb->prepare(
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
}
