<?php
namespace SD\Currency\Service;

use SD\Currency\Model\Money;

interface FormatterInterface
{
    public function formatMoney(Money $money): string;
}
