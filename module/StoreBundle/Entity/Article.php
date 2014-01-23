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

namespace StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Article
{
    /**
     * Factory Only
     */
    public function __construct()
    {
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Factory Only
     *
     * @param string $name
     *
     * @return \StoreBundle\Entity\Storage
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @var string The name of this storage
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @return float
     */
    public function getPortionsInCountingUnit()
    {
        
    }
    
    /**
     * @param float $portionsInCountingUnit
     */
    public function setPortionsInCountingUnit($portionsInCountingUnit)
    {
        
    }
    
    /**
     * @return \StoreBundle\Entity\UnitChain
     */
    public function getUnitChain()
    {
        
    }
    
    /**
     * Factory only
     * 
     * @param \StoreBundle\Entity\UnitChain $unitChain
     * 
     * @return \StoreBundle\Entity\UnitChain
     */
    public function setUnitChain($unitChain)
    {
    }
}
