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
 * This interface represents a map that allows objects to be used as keys.
 * 
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
abstract class Map
{
    
    /**
     * Returns a element from the map with key $key
     * 
     * Precondition: hasKey($key)
     * Precondition: $key is not null and not a array
     * 
     * @param mixed $key
     * 
     * @return  mixed
     */
    public function get($key)
    {
        return $this->getItem($this->getHash($key));
    }
    
    /**
     * Precondition: $hash is no object and not null and not a array
     * 
     * @param mixed $hash
     * 
     * @return  mixed
     */
    protected abstract function getItem($hash);
    
    /**
     * Returns a element from the map with key $key. If there is no such
     * element, a new element will be created, added to the map and returned.
     * 
     * Precondition: $key is not null and not a array
     * 
     * @param mixed $key
     * 
     * @param \CommonBundle\Component\Map\MapFactory $factory
     *              The factory that creates the new object.
     * 
     * @return  mixed
     */
    public function getOrCreate($key, $factory)
    {
        $hash = $this->getHash($key);
        if(!$this->hasItemWithHash($hash))
        {
            $value = $factory->create();
            $this->setItem($hash, $value);
        }
        
        return $this->getItem($hash);
    }
    
    /**
     * Adds a element with key $key.
     * 
     * Precondition: $key is not null and not a array
     * 
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->setItem($this->getHash($key), $value);
    }
    
    /**
     * Precondition: $hash is no object and not null and not a array
     * 
     * @param mixed $hash
     * @param mixed $value
     */
    protected abstract function setItem($hash, $value);
    
    /**
     * Returns true if the map contains the key $key. Otherwise false.
     * 
     * Precondition: $key is not null and not a array
     * 
     * @param mixed $key
     * 
     * @return boolean
     */
    public function hasKey($key)
    {
        return $this->hasItemWithHash($this->getHash($key));
    }
    
    /**
     * Precondition: $hash is no object and not null and not a array
     *
     * @param mixed $hash
     */
    protected abstract function hasItemWithHash($hash);
    
    /**
     * Precondition: $key is not null and not a array
     * 
     * Postcondition: return is not null and no object and not a array
     * 
     * @param mixed $key
     * 
     * @return mixed
     */
    private function getHash($key)
    {
        if(is_object($key))
            return spl_object_hash($key);
        else
            return $key;
    }
}
