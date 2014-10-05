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
namespace CommonBundle\Test\Entity\General;

use CommonBundle\Entity\General\Bank\BankDevice,
    CommonBundle\Entity\General\Bank\BankDevice\Amount as BAmount,
    CommonBundle\Entity\General\Bank\CashRegister,
    CommonBundle\Entity\General\Bank\MoneyUnit,
    CommonBundle\Entity\General\Bank\MoneyUnit\Amount as MAmount;

class BankTest extends \PHPUnit_Framework_TestCase
{
    public function testBank()
    {
        $cashRegister = new CashRegister();

        $bankDevice1 = new BankDevice('Device1');
        $bankDevice2 = new BankDevice('Device2');

        $ba1_1 = new BAmount($cashRegister, $bankDevice1, 10);
        $ba1_2 = new BAmount($cashRegister, $bankDevice1, 3.5);

        $ba2_1 = new BAmount($cashRegister, $bankDevice2, 5);
        $ba2_2 = new BAmount($cashRegister, $bankDevice2, 3);

        $moneyUnit1 = new MoneyUnit(0.1);
        $moneyUnit2 = new MoneyUnit(2);

        $ca1_1 = new MAmount($cashRegister, $moneyUnit1, 0);
        $ca1_2 = new MAmount($cashRegister, $moneyUnit1, 4);

        $ca2_1 = new MAmount($cashRegister, $moneyUnit2, 15);
        $ca2_2 = new MAmount($cashRegister, $moneyUnit2, 83);

        // $this->assertEquals($expected, $cashRegister->);
        $this->assertEquals('Device1', $bankDevice1->getName());
        $this->assertEquals('Device2', $bankDevice2->getName());
        $this->assertEquals(10, $moneyUnit1->getUnit());
        $this->assertEquals(200, $moneyUnit2->getUnit());

        $this->assertEquals(1000, $ba1_1->getAmount());
        $this->assertEquals(350, $ba1_2->getAmount());
        $this->assertEquals(500, $ba2_1->getAmount());
        $this->assertEquals(300, $ba2_2->getAmount());

        $this->assertEquals(0, $ca1_1->getValue());
        $this->assertEquals(40, $ca1_2->getValue());
        $this->assertEquals(3000, $ca2_1->getValue());
        $this->assertEquals(16600, $ca2_2->getValue());

        $this->assertEquals(21790, $cashRegister->getTotalAmount());
    }
}
