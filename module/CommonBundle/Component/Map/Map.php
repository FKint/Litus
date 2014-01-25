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

use Closure, Countable, IteratorAggregate, ArrayAccess;

/**
 * This interface represents a map that allows objects to be used as keys.
 * 
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
abstract class Map implements Countable, ArrayAccess, IteratorAggregate
{
    public function __construct()
    {
        $this->keys = array();
    }
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
    
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     * Precondition: $hash is no object and not null and not a array
     * Precondition: hasItemWithHash($hash)
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
        if(!$this->hasKey($key))
        {
            $value = $factory->create();
            $this->set($key, $value);
        }    

        return $this->get($key);
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
        $hash = $this->getHash($key);
        $this->keys[$hash] = $key;
        $this->setItem($hash, $value);
    }
    
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
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
    
    public function offsetExists($offset)
    {
        return $this->hasKey($offset);
    }
    
    /**
     * Precondition: $hash is no object and not null and not a array
     *
     * @param mixed $hash
     */
    protected abstract function hasItemWithHash($hash);
    
    /**
     * Removes the element with key $key from the map.
     * 
     * Precondition: hasKey($key)
     * Precondition: $key is not null and not a array
     * 
     * @param mixed $key
     */
    public function remove($key)
    {
        $hash = $this->getHash($key);
        unset ($this->keys[$hash]);
        $this->removeItem($hash);
    }
    
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
    
    /**
     * Precondition: $hash is no object and not null and not a array
     * Precondition: hasItemWithHash($hash)
     *
     * @param mixed $hash
     */
    protected abstract function removeItem($hash);
    
    public function getIterator()
    {
        return new MapIterator($this->getIter(), $this->keys);
    }
    
    protected abstract function getIter();
    
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
    
    /**
     * @var array
     */
    private $keys;
    
    /**
     * @return boolean
     */
    public abstract function isEmpty();
    
    /**
     * Sets the internal iterator to the first element in the collection
     * and returns this element.
     * 
     * Precondition: !isEmpty()
     * 
     * @return mixed
     */
    public abstract function getFirst();
}
