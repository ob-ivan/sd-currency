<?php
namespace tests\DependencyInjection;

use SD\Currency\Store\FileStore;

class MockFileStore extends FileStore
{
    private $dir;

    public function __construct(string $dir)
    {
        parent::__construct($dir);
        $this->dir = $dir;
    }

    public function getDir(): string {
        return $this->dir;
    }
}
