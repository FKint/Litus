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
    StoreBundle\Component\StockCount\AbstractCount,
    StoreBundle\Component\Valuta\ValutaFactory;

/**
 * This class is responsible for storing StockCountTuples and calculating
 * the cost and theoretical income.
 *
 * For making this calculation, the tuple will be passed along a chain of
 * AmountCounts. This chain will be terminated by a ValueCount.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class StockCount
{
    /**
     * 
     * @var \StoreBundle\Component\StockCount\ArticleCount
     */
    private $articleCount;
    
    /**
     * Constructor only
     * 
     * @param \StoreBundle\Component\StockCount\ArticleCount $articleCount
     */
    public function __construct($articleCount)
    {
        $this->articleCount = $articleCount;
    }
    
    /**
     * Adds a tuplevalue to the structure.
     * 
     * Precondition: $tuple.getValue() != null
     * Precondition: Every field that is processed can not be null
     * 
     * @param \StoreBundle\Entity\StockCountTuple $tuple
     */
    public function addCountTuple($tuple)
    {
        $this->articleCount->addCountTuple($tuple);
    }
    
    public function getIncome()
    {
        return $this->articleCount->getIncome();
    }

    public function getCost()
    {
        return $this->articleCount->getCost();
    }
}