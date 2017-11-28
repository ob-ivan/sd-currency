<?php
namespace SD\Currency\Model;

class Currency {
    private $code;
    private $unicode;
    private $html;

    private function __construct(string $code, string $unicode, string $html) {
        $this->code = $code;
        $this->unicode = $unicode;
        $this->html = $html;
    }

    public function getCode() {
        return $this->code;
    }

    public function getUnicode() {
        return $this->unicode;
    }

    public function getHtml() {
        return $this->html;
    }
}
