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

namespace StoreBundle\Factory;

use StoreBundle\Entity\Unit;
use StoreBundle\Entity\UnitType;

/**
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class UnitFactory
{
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
    
    public function createCountUnitType($name)
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
}