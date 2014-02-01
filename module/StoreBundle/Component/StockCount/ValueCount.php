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

use Doctrine\ORM\Mapping as ORM;

/**
 * This class terminates the count chain. It simply returns the value it
 * received.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class ValueCount implements AmountCount
{
    /**
     * @var float | integer
     */
    private $value;
    
    /**
     * (non-PHPdoc)
     * @see \StoreBundle\Entity\StockCount\AmountCount::setTupleValue()
     */
    public function setTupleValue($tuple, $value)
    {
        $this->value = $value;
    }

    /**
     * (non-PHPdoc)
     * @see \StoreBundle\Entity\StockCount\AmountCount::getAmount()
     */
    public function getAmount($unitChain)
    {
        return $this->value;
    }
}