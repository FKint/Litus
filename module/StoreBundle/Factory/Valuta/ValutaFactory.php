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

namespace StoreBundle\Factory\Valuta;

use StoreBundle\Entity\Valuta\Valuta;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class ValutaFactory
{
    /**
     * @param integer | float | double $incl
     * @param integer | float | double $btw
     *
     * Precondition: $btw != -1
     *
     * @return \StoreBundle\Factory\Valuta\Valuta
     */
    public function createIncl($incl, $btw)
    {
        $r = new Valuta();
        $r->setIncl($incl);
        $r->setExcl($incl / (1 + $btw));
        return $r;
    }

    /**
     * @param integer | float | double $excl
     * @param integer | float | double $btw
     *
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function createExcl($excl, $btw)
    {
        $r = new Valuta();
        $r->setIncl($excl * (1 + $btw));
        $r->setExcl($excl);
        return $r;
    }

    /**
     *
     * @param integer | float | double $incl
     * @param integer | float | double $excl
     *
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function create($incl, $excl)
    {
        $r = new Valuta();
        $r->setIncl($incl);
        $r->setExcl($excl);
        return $r;
    }

    public function create0()
    {
        return $this->create(0, 0);
    }
}