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

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Opening
{
    private $stockCount;
    
    private $openingData;
    
    /**
     * Factory Only
     */
    public function __construct()
    {
    }

    public function getOpeningData()
    {
        return $this->openingData;
    }
    
    public function setOpeningData($openingData)
    {
        $this->openingData = $openingData;
    }
    
    public function getStockCount()
    {
        return $this->stockCount;
    }
    
    public function setStockCount($stockCount)
    {
        $this->stockCount = $stockCount;
    }
    
    public function getCashCount()
    {
        return $this->openingData->getCashCount();
    }
    
    public function getDelta()
    {
        return $this->getIncome()->subtract($this->getExpectedIncome());
    }
    
    public function getCost()
    {
        return $this->getStockCount()->getCost();
    }
    
    public function getIncome()
    {
        return $this->getCashCount()->getIncome();
    }
    
    public function getProfit()
    {
        return $this->getIncome()->subtract($this->getCost());
    }
    
    public function getExpectedIncome()
    {
        return $this->stockCount->getIncome();
    }
}
