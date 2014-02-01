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

namespace StoreBundle\Component\Unit;

use StoreBundle\Entity\Unit\Unit,
    StoreBundle\Entity\Unit\UnitType;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class UnitFactory
{
    /**
     * The last UnitType that was created.
     * 
     * @var \StoreBundle\Entity\Unit\UnitType
     */
    private $last;
    
    public function __construct()
    {
        $this->last = null;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @param string $name The name of the unit
     *
     * @return \StoreBundle\Entity\Unit
     */
    public function createUnit($name)
    {
        $unit = new Unit();
        $unit->setName($name);

        return $unit;
    }

    public function createPortionUnitType($name)
    {
        return $this->createUnitType($name, null);
    }

    public function createUnitType($name, $subUnitType)
    {
        $unitType = new UnitType();
        $unitType->setName($name);
        $unitType->setSubType($subUnitType);

        return $unitType;
    }

    /**
     * Creates a new unit type. The first time you invoke this methode, a
     * portion unit type will be returned, the next invokation, a unit type
     * will be created with that portion unit type as subtype. The next one
     * created will have the previously created unit type as its subtype.
     *
     * @param string $name
     */
    public function createNextChainedUnitType($name)
    {
        $this->last = $this->createUnitType($name, $this->last);
        return $this->last;
    }
}