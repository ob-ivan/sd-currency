<?php
namespace tests\DependencyInjection;

use SD\Currency\DependencyInjection\CurrencyAwareTrait;
use SD\DependencyInjection\AutoDeclarerInterface;
use SD\DependencyInjection\AutoDeclarerTrait;

class ParentConsumer implements AutoDeclarerInterface
{
    use AutoDeclarerTrait;
    use CurrencyAwareTrait;
}
