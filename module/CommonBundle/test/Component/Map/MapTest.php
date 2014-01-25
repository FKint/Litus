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

use CommonBundle\Component\Map\MapFactory;
use CommonBundle\Component\Map\DoctrineMap;
use CommonBundle\Component\Map\ArrayMap;

class MapTest extends PHPUnit_Framework_TestCase
{
    public function testArrayMap()
    {
        $this->tMap(new ArrayMap());
    }
    
    public function testDoctrineMap()
    {
        $this->tMap(new DoctrineMap());
    }
    
    /**
     * 
     * @param CommonBundle\Component\Map\Map $map
     */
    private function tMap($map)
    {
        $k1 = new O("k1");
        $k2 = new O("k2");
        $k3 = new O("k2");
        
        $v1 = new O("v1");
        $v2 = new O("v2");
        $v3 = new O("v3");
        
        $this->assertFalse($map->hasKey($k1));
        $this->assertFalse($map->hasKey($k2));
        $this->assertFalse($map->hasKey($k3));
        
        $map->set($k1, $v1);
        
        $this->assertTrue($map->hasKey($k1));
        $this->assertFalse($map->hasKey($k2));
        $this->assertFalse($map->hasKey($k3));
        
        $this->assertEquals($v1, $map->get($k1));
        
        $map->set($k2, $v2);
        
        $this->assertTrue($map->hasKey($k1));
        $this->assertTrue($map->hasKey($k2));
        $this->assertFalse($map->hasKey($k3));
        
        $this->assertEquals($v1, $map->get($k1));
        $this->assertEquals($v2, $map->get($k2));
        
        $map->getOrCreate($k3, new F($v3));
        
        $this->assertTrue($map->hasKey($k1));
        $this->assertTrue($map->hasKey($k2));
        $this->assertTrue($map->hasKey($k3));
        
        $this->assertEquals($v1, $map->get($k1));
        $this->assertEquals($v2, $map->get($k2));
        $this->assertEquals($v3, $map->get($k3));
    }
}

class F implements MapFactory
{
    public function __construct($o)
    {
        $this->o = $o;
    }
    
    private $o;
    
    public function create()
    {
        return $this->o;
    }
}

class O
{
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    private $name;
}
