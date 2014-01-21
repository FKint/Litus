<?php
use StoreBundle\Entity\Store;
use StoreBundle\Factory\StoreFactory;

class StoreTest extends PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $f = new StoreFactory();
        $s = $f->createStore("Test");
        $this->assertEquals("Test", $s->getName());
    }
    
    public function testEditAndUse()
    {
    	$t1 = "t";
    	$t2 = "f";
    	
    	$s = new Store();
    	$s->addEditRole($t1);
    	
    	$this->assertTrue($s->canBeEditedByRole($t1));
    	$this->assertFalse($s->canBeEditedByRole($t2));
    }
}
