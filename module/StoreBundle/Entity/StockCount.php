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

namespace StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class StockCount extends AbstractCount
{
    public function __construct()
    {
    }
    
    public function setTupleValue($tuple, $value)
    {        
    }
    
    public function getIncome()
    {
        
    }
    
    public function getCost()
    {
        
    }
    
    
}

abstract class AbstractCount
{
    public function __construct($nextFactory)
    {
        $this->nextFactory = $nextFactory;
    }
    
    public function setTupleValue($tuple, $value)
    {
        $i = $this->selectTupleItem($tuple);
        if(!array_key_exists($i, $this->map))
        {
            $this->map[$i] = $this->nextFactory->create();
        }
        
        $this->map[$i]->setTupleValue($tuple, $value);
    }
    
    private $nextFactory;
    
    protected abstract function selectTupleItem($tuple);
    
    private $map;
}