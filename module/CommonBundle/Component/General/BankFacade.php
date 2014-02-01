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

namespace CommonBundle\Component\General;

use CommonBundle\Entity\General\Bank\CashRegister;
use CommonBundle\Entity\General\Bank\MoneyUnit;
use CommonBundle\Entity\General\Bank\MoneyUnit\Amount;
use CommonBundle\Component\Map\Map;
use CommonBundle\Component\Map\ArrayMapFactory;

/**
 * This class is a facade for the enities in the
 * CommonBundle\Entity\General\Bank - namespace.
 */
class BankFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Keeps track of what cash registers maps to whay money amounts.
     * 
     * @var \CommonBundle\Component\Map\Map
     */
    private $map;
    
    /**
     * @var \CommonBundle\Component\Map\ArrayMapFactory
     */
    private $factory;
    
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
        $this->factory = new ArrayMapFactory();
    }
    
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }
    
    /**
     * @return \CommonBundle\Entity\General\Bank\CashRegister
     */
    public function createCashRegister()
    {
        return new CashRegister();
    }
    
    /**
     * @param CashRegister $cashRegister
     * @param MoneyUnit $moneyUnit
     * @param integer $amount
     */
    public function addAmountToCashRegister(CashRegister $cashRegister
            , MoneyUnit $moneyUnit, $amount)
    {
        $a = new Amount($cashRegister, $moneyUnit, $amount);
        $amounts = $this->map->getOrCreate($cashRegister, $this->factory);
        $amounts[] = $a;
    }
    
    /**
     * @param \CommonBundle\Entity\General\Bank\CashRegister $cashRegister
     */
    public function persistCashRegisterAndItsAmounts($cashRegister)
    {
        $this->entityManager->persist($cashRegister);
        
        $amounts = $this->map->getOrCreate($cashRegister, $this->factory);
        foreach($amounts as $amount)
        {
            $this->entityManager->persist($amount);
        }
    }
    
    /**
     * @return array
     */
    public function getAllMoneyUnits()
    {
        return $this->getEntityManager()
            ->getRepository("CommonBundle\Repository\General\Bank\MoneyUnit")
            ->findAll();
    }
}
