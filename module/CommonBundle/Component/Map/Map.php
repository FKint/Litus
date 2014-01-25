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

namespace CommonBundle\Component\Map;


/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
abstract class Map
{
    public function get($key)
    {
        $hash = spl_object_hash($key);
        return $this->getItem($hash);
    }
    
    protected abstract function getItem($hash);
    
    public function getOrCreate($key, $factory)
    {
        $hash = spl_object_hash($key);
        if(!$this->hasItemWithHash($hash))
        {
            $value = $factory->create();
            $this->setItem($hash, $value);
        }
       
        return $this->getItem($hash);
    }
    
    public function set($key, $value)
    {
        $hash = spl_object_hash($key);
        $this->setItem($hash, $value);
    }
    
    protected abstract function setItem($hash, $value);
    
    public function hasKey($key)
    {
        $hash = spl_object_hash($key);
        return $this->hasItemWithHash($hash);
    }
    
    protected abstract function hasItemWithHash($hash);
}
