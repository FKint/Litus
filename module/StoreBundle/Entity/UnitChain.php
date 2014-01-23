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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class UnitChain
{
    public function __construct()
    {
        $this->map = new ArrayCollection();
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
     * @ORM\Column(chain="bigint")
     */
    private $id;
    
    /**
     * See unit tests for effect.
     * 
     * Precondition: canAddToChain($unitType)
     * Precondition: $unitType->getId() is set and unique within the chain
     * 
     * @param \StoreBundle\Entity\UnitType $unitType
     * @param integer $nbOfSubUnitsInUnit
     */
    public function addUnitTypeToChain($unitType, $nbOfSubUnitsInUnit)
    {
        $this->map[$unitType->getId()] = new UnitChainLink($unitType, $nbOfSubUnitsInUnit);
    }
    
    /**
     * True if $unitType->getCountSubUnit() equals the count sub unit of each
     * of the unitTypes already in the chain. It is allowed to make a gab
     * in the chain, if it will be filled later.
     * 
     * @param \StoreBundle\Entity\UnitType $unitType
     */
    public function canAddToChain($unitType)
    {
        if($this->map->isEmpty())
            return true;
        
        return $this->map->first()->getUnitType()->getPortionSubType() === $unitType->getPortionSubType();
    }
    
    /**
     * Returns true if the chain contains a gab between the unitType and the
     * countingUnit. Returns also true if there is no $unitType added to the 
     * chain.
     * 
     * Precondition: $unitType->getId() is set and unique within the chain
     * 
     * @param    \StoreBundle\Entity\UnitType $unitType
     * 
     * @return boolean
     */
    public function containsGab($unitType)
    {
        if($this->map->containsKey($unitType->getId())) {
            if($unitType->isPortionType())
                return false;
                
            return $this->containsGab($unitType->getSubType());
        }
        
        return true;
    }
    
    /**
     * See unit tests for usage.
     * 
     * Precondition !containsGab($unitType)
     * Precondition: $unitType->getId() is set and unique within the chain
     * 
     * @param \StoreBundle\Entity\UnitType $unitType
     */
    public function getNbPortionsInUnitType($unitType)
    {
        if($unitType->isPortionType())
            return $this->map[$unitType->getId()]->getNb();
        
        return $this->map[$unitType->getId()]->getNb() * $this->getNbPortionsInUnitType($unitType->getSubType());
    }
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $map;
}

class UnitChainLink
{
    public function __construct($unitType, $nb)
    {
        $this->unitType = $unitType;
        $this->nb = $nb;
    }
    
    public function getUnitType()
    {
        return $this->unitType;
    }
    
    protected function setUnitType($unitType)
    {
        $this->unitType = $unitType;
        return $this;    
    }
    
    private $unitType;
    
    
    public function getNb()
    {
        return $this->nb;
    }
    
    protected function setNb($nb)
    {
        $this->nb = $nb;
        return $this;
    }
    
    private $nb;
    
}





