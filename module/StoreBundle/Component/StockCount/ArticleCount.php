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
class ArticleCount
{
    /**
     * @var \StoreBundle\Component\StockCount\ArticleCount
     */
    private $articleCount;
    
    /**
     * @var \StoreBundle\Component\StockCount\StockCountTuple[]
     */
    private $stockCountTuples;
    
    public function getIncome()
    {
        
    }
    
    public function getCost()
    {
        
    }
    
    
}