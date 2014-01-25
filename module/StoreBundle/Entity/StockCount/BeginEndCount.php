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

use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Factory\Valuta\ValutaFactory;

/**
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class BeginEndCount implements AmountCount
{
    public function __construct($nextFactory)
    {
        $this->begin = $nextFactory->create();
        $this->end = $nextFactory->create();
    }
    
    public function getAmount($unitChain)
    {
        $b = $this->begin->getAmount($unitChain);
        $e = $this->end->getAmount($unitChain);
        
        return $b - $e;
    }
    
    public function setTupleValue($tuple, $value)
    {
        if($tuple->isBeginCount())
            $this->begin->setTupleValue($tuple, $value);
        else
            $this->end->setTupleValue($tuple, $value);
    }
        
    
    /**
     * @var \StoreBundle\Entity\StockCount\AmountCount
     */
    private $begin;
    
    /**
     * @var \StoreBundle\Entity\StockCount\AmountCount
     */
    private $end;
}