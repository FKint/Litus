<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

use StoreBundle\Entity\Store;
use StoreBundle\Component\StoreFactory;

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
