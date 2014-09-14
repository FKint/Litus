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

use CommonBundle\Entity\General\Location;
use CommonBundle\Entity\General\Address;

class LocationTest extends \PHPUnit_Framework_TestCase
{
    public function testLocation()
    {
        $address = new Address('Studentenwijk Arenberg', '6', '0', '3001', 'Heverlee', 'BE');
        $loc = new Location('Block 6', $address, '50.868550', '4.687454');
        $this->assertEquals('Block 6', $loc->getName());
        $this->assertEquals($address, $loc->getAddress());
        $this->assertEquals('50.868550', $loc->getLatitude());
        $this->assertEquals('4.687454', $loc->getLongitude());
        $this->assertTrue($loc->isActive());
        
        $loc->deactivate();
        $this->assertEquals('Block 6', $loc->getName());
        $this->assertEquals($address, $loc->getAddress());
        $this->assertEquals('50.868550', $loc->getLatitude());
        $this->assertEquals('4.687454', $loc->getLongitude());
        $this->assertFalse($loc->isActive());
    }
}
