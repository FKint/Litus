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
 * @ORM\Entity
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class UnitChain
{
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
     * @ORM\Column(chain="bigint")
     */
    private $id;
    
    /**
     * See unit tests for effect.
     * 
     * Precondition: isChainable($unitType)
     * 
     * @param StoreBundle\Entity\UnitType $unitType
     * @param integer $nbOfSubUnitsInUnit
     */
    public function addUnitTypeToChain($unitType, $nbOfSubUnitsInUnit)
    {
    	
    }
    
    /**
     * $unitType->getSubType creates a chain recursivly.
     * 
     * It is only possible to add a unitType to the chain if it can be added
     * at the end or at the front of the chain. It is allowed to make a gab
     * in the chain, if it will be filled later.
     * 
     * @param StoreBundle\Entity\UnitType $unitType
     */
    public function canAddToChain($unitType)
    {
    	
    }
    
    /**
     * Returns true if the chain contains a gab between the unitType and the
     * countingUnit. Returns also true if there is no $unitType added to the 
     * chain.
     * 
     * @return boolean
     */
    public function containsGab($unitType)
    {
    	
    }
    
    /**
     * See unit tests for usage.
     * 
     * Precondition !containsGab($unitType)
     * 
     * @param StoreBundle\Entity\UnitType $unitType
     */
    public function getNbCountingUnitsInUnitType($unitType)
    {
    	
    }
}
