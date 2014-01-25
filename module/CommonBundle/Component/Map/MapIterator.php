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

use \Iterator;
/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class MapIterator implements Iterator
{
    public function __construct($iterator, $keys)
    {
        $this->iterator = $iterator;
        $this->keys = $keys;
    }
    
    /**
     * 
     * @var Iterator
     */
    private $iterator;
    
    /**
     * @var array
     */
    private $keys;
    
    public function current()
    {
        return $this->iterator->current();
    }
    
    public function key()
    {
        return $this->keys[$this->iterator->key()];
    }
    
    public function rewind() {
        $this->iterator->rewind();
    }

    function next() {
        $this->iterator->next();
    }

    function valid() {
        return $this->iterator->valid();
    }
}
