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

use CommonBundle\test\Entity\General\Bank\TestCashRegister,
    StoreBundle\Component\CashCount;

class CashCountTest extends PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $cr1 = new TestCashRegister(120);
        $cr2 = new TestCashRegister(150);
        $cc = new CashCount($cr1, $cr2);
        
        $this->assertEquals(30, $cc->getIncome());
        
        $this->assertEquals($cr1, $cc->getCashRegisterBegin());
        $this->assertEquals($cr2, $cc->getCashRegisterEnd());
        
        $this->assertEquals($cr1, $cc->getCashRegister(true));
        $this->assertEquals($cr2, $cc->getCashRegister(false));
    }
}
