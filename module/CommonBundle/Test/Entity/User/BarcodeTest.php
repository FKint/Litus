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
namespace CommonBundle\Test\Entity\General\Organization;

use CommonBundle\Entity\User\Barcode,
    CommonBundle\Test\Entity\User\Person\TestPerson;

class BarcodeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $person = new TestPerson();
        $bc = new Barcode($person, 112358132134);
        
        $this->assertEquals(112358132134, $bc->getBarcode());
        $this->assertEquals($person, $bc->getPerson());
    }

    public function test13DigitBarcode()
    {
        $person = new TestPerson();
        $bc = new Barcode($person, 1234567890123);
        $this->assertEquals(123456789012, $bc->getBarcode());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTooBigBarcode()
    {
        $person = new TestPerson();
        $bc = new Barcode($person, 12345678901234);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTooShortBarcode()
    {
        $person = new TestPerson();
        $bc = new Barcode($person, 12345678901);
    }
}
