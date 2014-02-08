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

use StoreBundle\Component\Storage;
use StoreBundle\Component\StorageFactory;
use StoreBundle\Component\Article\ArticleFactory;
use StoreBundle\Component\Unit\UnitFactory;
use StoreBundle\Component\Valuta\ValutaFactory;
use CommonBundle\Entity\General\Bank\CashRegister;
use CommonBundle\Entity\General\Bank\MoneyUnit;
use CommonBundle\Entity\General\Bank\MoneyUnit\Amount;
use CommonBundle\Component\General\BankFactory;

class CashRegisterTest extends PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $bf = new BankFactory();
        
        $u01 = new MoneyUnit(0.01);
        $u05 = new MoneyUnit(0.05);
        $u20 = new MoneyUnit(20);
        
        $cr = new CashRegister();
        
        $bf->createMoneyAmount($cr, $u01, 12);
        $bf->createMoneyAmount($cr, $u05, -53);
        $bf->createMoneyAmount($cr, $u20, 3);
        
        $this->assertEquals(57.47, $cr->getValueTotalAmount());
    }
}
