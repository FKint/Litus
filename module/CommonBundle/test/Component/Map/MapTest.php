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
        $this->tObjects(new ArrayMap());
        $this->tScalar(new ArrayMap());
    }
    
    public function testDoctrineMap()
    {
        $this->tObjects(new DoctrineMap());
        $this->tScalar(new ArrayMap());
    }
    
    private function tScalar($map)
    {
        $k = array();
        $k[1] = "k1";
        $k[2] = "k2";
        $k[3] = "k3";
        
        $this->t($map, $k);
    }
    
    /**
     *
     * @param CommonBundle\Component\Map\Map $map
     */
    private function tObjects($map)
    {
        $k = array();
        $k[1] = new O("k1");
        $k[2] = new O("k2");
        $k[3] = new O("k3");
        
        $this->t($map, $k);
    }
    
    /**
     * 
     * @param CommonBundle\Component\Map\Map $map
     */
    private function t($map, $k)
    {        
        $v1 = new O("v1");
        $v2 = new O("v2");
        $v3 = new O("v3");
        
        $this->assertFalse($map->hasKey($k[1]));
        $this->assertFalse($map->hasKey($k[2]));
        $this->assertFalse($map->hasKey($k[3]));
        
        $map->set($k[1], $v1);
        
        $this->assertTrue($map->hasKey($k[1]));
        $this->assertFalse($map->hasKey($k[2]));
        $this->assertFalse($map->hasKey($k[3]));
        
        $this->assertEquals($v1, $map->get($k[1]));
        
        $map->set($k[2], $v2);
        
        $this->assertTrue($map->hasKey($k[1]));
        $this->assertTrue($map->hasKey($k[2]));
        $this->assertFalse($map->hasKey($k[3]));
        
        $this->assertEquals($v1, $map->get($k[1]));
        $this->assertEquals($v2, $map->get($k[2]));
        
        $map->getOrCreate($k[3], new F($v3));
        
        $this->assertTrue($map->hasKey($k[1]));
        $this->assertTrue($map->hasKey($k[2]));
        $this->assertTrue($map->hasKey($k[3]));
        
        $this->assertEquals($v1, $map->get($k[1]));
        $this->assertEquals($v2, $map->get($k[2]));
        $this->assertEquals($v3, $map->get($k[3]));
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
