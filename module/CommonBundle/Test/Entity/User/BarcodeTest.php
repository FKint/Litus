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
namespace CommonBundle\Test\Entity\User;

use CommonBundle\Entity\User\Barcode,
    CommonBundle\Entity\User\Barcode\Ean12,
    CommonBundle\Test\Entity\User\Person\TestPerson;

class BarcodeTest extends \PHPUnit_Framework_TestCase
{
    public function testEan12Constructor()
    {
        $person = new TestPerson();
        $bc = new Ean12($person, 112358132134);

        $this->assertEquals(112358132134, $bc->getBarcode());
        $this->assertEquals($person, $bc->getPerson());
    }

    public function testEan12With13DigitBarcode()
    {
        $person = new TestPerson();
        $bc = new Ean12($person, 1234567890123);
        $this->assertEquals(123456789012, $bc->getBarcode());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTooBigEan12Barcode()
    {
        $person = new TestPerson();
        $bc = new Ean12($person, 12345678901234);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTooShortEan12Barcode()
    {
        $person = new TestPerson();
        $bc = new Ean12($person, 12345678901);
    }
}
