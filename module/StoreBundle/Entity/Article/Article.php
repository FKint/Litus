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

namespace StoreBundle\Entity\Article;

use Doctrine\ORM\Mapping as ORM,
    StoreBundle\Entity\Valuta\Valuta;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Article
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;
    
    /**
     * @var string The name of this storage
     *
     * @ORM\Column(type="string")
     */
    private $name;
    
    /**
     * @var \StoreBundle\Entity\UnitChain
     */
    private $unitChain;
    
    /**
     * @var \StoreBundle\Entity\Valuta\Valuta
     */
    private $sellingPrice;
    
    /**
     * @var \StoreBundle\Entity\Valuta\Valuta
     */
    private $purchasePrice;
    
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
     * @return \StoreBundle\Entity\UnitChain
     */
    public function getUnitChain()
    {
        return $this->unitChain;
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
        $this->unitChain = $unitChain;
    }

    /**
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function getSellingPrice()
    {
        return $this->sellingPrice;
    }

    /**
     * @param \StoreBundle\Entity\Valuta\Valuta $sellingPrice
     */
    public function setSellingPrice($sellingPrice)
    {
        $this->sellingPrice = $sellingPrice;
    }
    
    /**
     * Returns the purchase price of one portion.
     *
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function getPurchasePricePortion()
    {
        return $this->purchasePrice;
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
        return $this->getPurchasePricePortion()->multiply(
            $this->getUnitChain()
                ->getNbPortionsInUnitType($unitType)
        );
    }

    /**
     * @param \StoreBundle\Entity\Valuta\Valuta $purchasePrice
     */
    public function setPurchasePricePortion($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * Precondition !getUnitChain()->containsGab($unitType)
     *
     * @param \StoreBundle\Entity\Valuta\Valuta $purchasePrice
     * @param \StoreBundle\Entity\UnitType $unitType
     */
    public function setPurchasePrice($purchasePrice, $unitType)
    {
        $this->setPurchasePricePortion(
            $purchasePrice->divide(
                $this->getUnitChain()
                    ->getNbPortionsInUnitType($unitType)
            )
        );
    }

    /**
     * @return \StoreBundle\Entity\Valuta\Valuta
     */
    public function getMarginPerPortion()
    {
        return $this->getSellingPrice()->subtract($this->getPurchasePricePortion());
    }
}
