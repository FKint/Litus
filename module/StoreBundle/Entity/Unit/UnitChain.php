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

namespace StoreBundle\Entity\Unit;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    CommonBundle\Component\Map\DoctrineMap;

/**
 * This class couples a UnitType to a number. This number is the amount of
 * units of UnitType::getSubType() in a UnitType. For example: a bottle crate
 * contains 24 bottles. 24 will be coupled to the bottle crate (is a Unit Type).
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class UnitChain
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(chain="bigint")
     */
    private $id;
    
    /**
     * @var \CommonBundle\Component\Map\Map
     */
    private $map;
    
    public function __construct()
    {
        $this->map = new DoctrineMap();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * See unit tests for effect.
     *
     * Precondition: canAddToChain($unitType)
     *
     * @param \StoreBundle\Entity\UnitType $unitType
     * @param integer $nbOfSubUnitsInUnit
     */
    public function addUnitTypeToChain($unitType, $nbOfSubUnitsInUnit)
    {
        $this->map->set($unitType, new UnitChainLink($unitType, $nbOfSubUnitsInUnit));
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

        return $this->map->getFirst()->getUnitType()->getPortionSubType() === $unitType->getPortionSubType();
    }

    /**
     * Returns true if the chain contains a gab between the unitType and the
     * countingUnit. Returns also true if there is no $unitType added to the
     * chain.
     *
     * @param    \StoreBundle\Entity\UnitType $unitType
     *
     * @return boolean
     */
    public function containsGab($unitType)
    {
        if($this->map->hasKey($unitType)) {
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
     *
     * @param \StoreBundle\Entity\UnitType $unitType
     */
    public function getNbPortionsInUnitType($unitType)
    {
        if($unitType->isPortionType())
            return $this->map->get($unitType)->getNb();

        return $this->map->get($unitType)->getNb() * $this->getNbPortionsInUnitType($unitType->getSubType());
    }
}




