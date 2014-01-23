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
use StoreBundle\Entity\Valuta\Valuta;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Article
{
    /**
     * Factory Only
     */
    public function __construct()
    {
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Factory Only
     *
     * @param string $name
     *
     * @return \StoreBundle\Entity\Storage
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @var string The name of this storage
     *
     * @ORM\Column(type="string")
     */
    private $name;
    
    /**
     * @return \StoreBundle\Entity\UnitChain
     */
    public function getUnitChain()
    {
    }
    
    /**
     * Factory only
     * 
     * @param \StoreBundle\Entity\UnitChain $unitChain
     * 
     * @return \StoreBundle\Entity\UnitChain
     */
    public function setUnitChain($unitChain)
    {
    }
    
    /**
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function getSellingPrice()
    {
    }
    
    /**
     * @param \StoreBundle\Entity\Valuta\Valuta $sellingPrice
     */
    public function setSellingPrice($sellingPrice)
    {
    }
    
    /**
     * Returns the purchase price of one portion.
     * 
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function getPurchasePricePortion()
    {
    }
    
    /**
     * Precondition !getUnitChain()->containsGab($unitType)
     * 
     * @param \StoreBundle\Entity\UnitType $unitType
     * 
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function getPurchasePrice($unitType)
    {
    }
    
    /**
     * @param \StoreBundle\Entity\Valuta\Valuta $purchasePrice
     */
    public function setPurchasePricePortion($purchasePrice)
    {
        
    }
    
    /**
     * Precondition !getUnitChain()->containsGab($unitType)
     * 
     * @param \StoreBundle\Entity\Valuta\Valuta $purchasePrice
     * @param \StoreBundle\Entity\UnitType $unitType
     */
    public function setPurchasePrice($purchasePrice, $unitType)
    {
    }
    
    private $purchasePrice;
    
    /**
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function getMarginPerPortion()
    {
        
    }
}
