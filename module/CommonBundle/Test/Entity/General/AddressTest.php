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

use CommonBundle\Entity\General\Address,
    CommonBundle\Entity\General\Address\City,
    CommonBundle\Entity\General\Address\Street;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    public function testCity()
    {
        $city = new City(666, 'The city of the beast');

        $street1 = new Street($city, 1, 'Ett');
        $street2 = new Street($city, 2, 'Två');
        $street3 = new Street($city, 3, 'Tre');
        $street4 = new Street($city, 4, 'Fyra');

        $this->assertEquals('Ett', $street1->getName());
        //$this->assertEquals(1, $street1->getRegisterNumber());
        //$this->assertEquals($city, $street1->getCity());

        $this->assertEquals('The city of the beast', $city->getName());
        $this->assertEquals(666, $city->getPostal());
        $this->assertEquals(4, count($city->getStreets()));
        $this->assertContains($street1, $city->getStreets());
        $this->assertContains($street2, $city->getStreets());
        $this->assertContains($street3, $city->getStreets());
        $this->assertContains($street4, $city->getStreets());
    }

    public function testAddressGoodCountry()
    {
        $address = new Address();
        $address->setStreet('Galärvarvsvägen');
        $address->setNumber('14');
        $address->setMailbox('');
        $address->setPostal('115 21');
        $address->setCity('Stockholm');
        $address->setCountry('SE');

        $this->assertEquals('Galärvarvsvägen', $address->getStreet());
        $this->assertEquals('14', $address->getNumber());
        $this->assertEquals('', $address->getMailbox());
        $this->assertEquals('115 21', $address->getPostal());
        $this->assertEquals('Stockholm', $address->getCity());
        $this->assertEquals('Sweden', $address->getCountry());
        $this->assertEquals('SE', $address->getCountryCode());
    }

    public function testAddressBadCountry()
    {
        $address = new Address();
        $address->setStreet('Galärvarvsvägen');
        $address->setNumber('14');
        $address->setMailbox('');
        $address->setPostal('115 21');
        $address->setCity('Stockholm');
        $address->setCountry('Sweden');

        $this->assertEquals('Galärvarvsvägen', $address->getStreet());
        $this->assertEquals('14', $address->getNumber());
        $this->assertEquals('', $address->getMailbox());
        $this->assertEquals('115 21', $address->getPostal());
        $this->assertEquals('Stockholm', $address->getCity());
        $this->assertEquals(null, $address->getCountry());
        $this->assertEquals(null, $address->getCountryCode());
    }
}
