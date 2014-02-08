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

namespace StoreBundle\Component\StockCount;

use Doctrine\ORM\Mapping as ORM,
    StoreBundle\Entity\Valuta\Valuta;

/**
 * All classes in the chain of StockCount will implement this interface.
 * All these classes (except for ValueCount) will be chained by means of
 * factories.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
interface AmountCount
{
    /**
     * This method is called to pass a tuple and it's value along the chain.
     * Most of the time will the implementation of this method call this method
     * on the next link in the chain.
     * 
     * Precondition: $tuple.getValue() != null
     * Precondition: Every field that is processed can not be null
     * 
     * @param \StoreBundle\Entity\StockCountTuple $tuple
     */
    public function addCountTuple($tuple);

    /**
     * Returns the amount that the next Count in the chain has calculated.
     * Most of the time will the result of this method be used in the
     * implementation of this method of the previous link in the chain.
     *
     * @param \StoreBundle\Entity\UnitChain $unitChain
     *
     * @return integer | float
     */
    public function getAmount($unitChain);
}