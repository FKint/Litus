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

namespace StoreBundle\Entity\StockCount;

use Doctrine\ORM\Mapping as ORM,
    StoreBundle\Factory\Valuta\ValutaFactory;

/**
 * This count handles the begin and end count.
 *
 * @see \StoreBundle\Entity\StockCount\AmountCount
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class BeginEndCount implements AmountCount
{
    /**
     * @param @see \StoreBundle\Entity\StockCount\AmountCount $nextFactory
     */
    public function __construct($nextFactory)
    {
        $this->begin = $nextFactory->create();
        $this->end = $nextFactory->create();
    }

    /**
     * (non-PHPdoc)
     * @see \StoreBundle\Entity\StockCount\AmountCount::getAmount()
     */
    public function getAmount($unitChain)
    {
        $b = $this->begin->getAmount($unitChain);
        $e = $this->end->getAmount($unitChain);

        return $b - $e;
    }

    /**
     * (non-PHPdoc)
     * @see \StoreBundle\Entity\StockCount\AmountCount::setTupleValue()
     */
    public function setTupleValue($tuple, $value)
    {
        if($tuple->isBeginCount())
            $this->begin->setTupleValue($tuple, $value);
        else
            $this->end->setTupleValue($tuple, $value);
    }


    /**
     * The begin count
     *
     * @var \StoreBundle\Entity\StockCount\AmountCount
     */
    private $begin;

    /**
     * The end count
     *
     * @var \StoreBundle\Entity\StockCount\AmountCount
     */
    private $end;
}