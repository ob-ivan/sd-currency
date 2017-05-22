<?php

class SD_Currency_Service_Store_Factory {
    public static function create() {
        return self::db();
    }

    public static function db() {
        return new SD_Currency_DbStore();
    }

    public static function file($dir) {
        return new SD_Currency_FileStore($dir);
    }
}
