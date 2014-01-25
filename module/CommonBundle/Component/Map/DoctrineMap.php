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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class DoctrineMap extends Map
{
    public function __construct()
    {
        $this->map = new ArrayCollection();
    }
    
    protected function getItem($hash)
    {
        return $this->map[$hash];
    }
    
    protected function setItem($hash, $value)
    {
        $this->map[$hash] = $value;
    }

    protected function hasItemWithHash($hash)
    {
        return $this->map->containsKey($hash);
    }
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $map;
}
