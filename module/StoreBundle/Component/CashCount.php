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

namespace StoreBundle\Component;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class CashCount
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
     * @var \CommonBundle\Entity\General\Bank\CashRegister
     */
    private $cashRegisterBegin;
    
    /**
     * @var \CommonBundle\Entity\General\Bank\CashRegister
     */
    private $cashRegisterEnd;

    /**
     * Factory Only
     */
    public function __construct($cashRegisterBegin, $cashRegisterEnd)
    {
        $this->cashRegisterBegin = $cashRegisterBegin;
        $this->cashRegisterEnd = $cashRegisterEnd;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getIncome()
    {
        return $this->cashRegisterEnd->getValueTotalAmount()
            - $this->cashRegisterBegin->getValueTotalAmount();
    }
    
    public function getCashRegisterBegin()
    {
        return $this->cashRegisterBegin;
    }
    
    public function getCashRegisterEnd()
    {
        return $this->cashRegisterEnd;
    }
    
    public function getCashRegister($isBegin)
    {
        if($isBegin)
            return $this->getCashRegisterBegin();
        else
            return $this->getCashRegisterEnd();
    }
}