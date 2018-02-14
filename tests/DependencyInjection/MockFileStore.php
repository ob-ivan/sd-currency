<?php
namespace tests\DependencyInjection;

use SD\Currency\Store\FileStore;
use SD\DependencyInjection\AutoDeclarerInterface;
use SD\DependencyInjection\AutoDeclarerTrait;
use SD\DependencyInjection\Container;
use SD\DependencyInjection\ContainerAwareTrait;

class MockFileStore extends FileStore implements AutoDeclarerInterface
{
    use AutoDeclarerTrait;
    use ContainerAwareTrait;

    private $dir;

    public function __construct(string $dir)
    {
        parent::__construct($dir);
        $this->dir = $dir;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getDir(): string
    {
        return $this->dir;
    }
}
