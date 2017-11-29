<?php
namespace SD\Currency\Model;

class Currency {
    const POSITION_BEFORE = 1;
    const POSITION_AFTER  = 2;

    private $code;
    private $unicode;
    private $html;

    public function __construct(string $code, string $unicode, string $html, $position) {
        $this->code = $code;
        $this->unicode = $unicode;
        $this->html = $html;
        $this->position = $position;
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

    public function getPosition() {
        return $this->position;
    }
}
