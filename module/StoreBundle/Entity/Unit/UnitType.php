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

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a unit type. Some examples: a bottle, a bottle crate,
 * a beer barrel, ... It is called a unit type because, for example, a bottle
 * of 20cl is a unit.
 *
 * A UnitType can also have a subtype. A example: a bottle crate has a bottle
 * as subtype because a bottle crate contains bottles. If a UnitType has no
 * subtype, we say it is a portion unit type.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class UnitType
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;
    
    /**
     * @var string The name of this unit type
     *
     * @ORM\Column(type="string")
     */
    private $name;
    
    /**
     * @var \StoreBundle\Entity\UnitType | null The subtype of this unit type
     *
     * @ORM\Column(type="string")
     */
    private $subType;
    
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
     * @param integer $id
     *
     * @return \StoreBundle\Entity\UnitType
     */
    protected function setId($id)
    {
        $this->id = $id;
        return $this;
    }    

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
     * @return \StoreBundle\Entity\UnitType
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return \StoreBundle\Entity\UnitType | null
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * Factory Only
     *
     * @param \StoreBundle\Entity\UnitType | null $subType
     *
     * @return \StoreBundle\Entity\UnitType
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;
        return $this;
    }

    /**
     * Returns true if this unitType is a portion type.
     *
     * @return boolean
     */
    public function isPortionType()
    {
        return $this->subType == false;
    }

    /**
     * @return \StoreBundle\Entity\UnitType
     */
    public function getPortionSubType()
    {
        if($this->isPortionType())
            return $this;

        return $this->getSubType()->getPortionSubType();
    }
}
