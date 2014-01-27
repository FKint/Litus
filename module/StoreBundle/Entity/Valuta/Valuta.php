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

namespace StoreBundle\Entity\Valuta;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a valuta and abstracts away all the inclusive /
 * exclusive problems.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Valuta
{
    public function __construct()
    {
    }

    /**
     * @return  integer | float | double
     */
    public function getIncl()
    {
        return $this->incl;
    }

    /**
     * Factory Only
     *
     * @param integer | float | double $incl
     */
    public function setIncl($incl)
    {
        $this->incl = $incl;
    }

    /**
     * @var integer | float | double
     */
    private $incl;

    /**
     * @return integer | float | double
     */
    public function getExcl()
    {
        return $this->excl;
    }

    /**
     * Factory Only
     *
     * @param integer | float | double $excl
     */
    public function setExcl($excl)
    {
        $this->excl = $excl;
    }

    /**
     * @var integer | float | double
     */
    private $excl;

    /**
     * @param \StoreBundle\Entity\Valuta\Valuta $valuta
     *
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function add($valuta)
    {
        $r = new Valuta();
        $r->setIncl($this->getIncl() + $valuta->getIncl());
        $r->setExcl($this->getExcl() + $valuta->getExcl());
        return $r;
    }

    /**
     * @param \StoreBundle\Entity\Valuta\Valuta $valuta
     *
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function subtract($valuta)
    {
        $r = new Valuta();
        $r->setIncl($this->getIncl() - $valuta->getIncl());
        $r->setExcl($this->getExcl() - $valuta->getExcl());
        return $r;
    }

    /**
     * @param integer | float | double $scalar
     *
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function multiply($scalar)
    {
        $r = new Valuta();
        $r->setIncl($this->getIncl() * $scalar);
        $r->setExcl($this->getExcl() * $scalar);
        return $r;
    }

    /**
     * @param integer | float | double $scalar
     *
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function divide($scalar)
    {
        $r = new Valuta();
        $r->setIncl($this->getIncl() / $scalar);
        $r->setExcl($this->getExcl() / $scalar);
        return $r;
    }
}
