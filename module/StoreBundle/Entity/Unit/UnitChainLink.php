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

class UnitChainLink
{
    private $unitType;
    private $nb;
    
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

    public function getNb()
    {
        return $this->nb;
    }

    protected function setNb($nb)
    {
        $this->nb = $nb;
        return $this;
    }
}





