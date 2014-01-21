<?php
use StoreBundle\Entity\Storage;
use StoreBundle\Factory\StorageFactory;

class StorageTest extends PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $f = new StorageFactory();
        $s = $f->createStorage("Test");
        $this->assertEquals("Test", $s->getName());
    }
}
