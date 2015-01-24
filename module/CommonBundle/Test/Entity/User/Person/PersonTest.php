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
namespace CommonBundle\Test\Entity\User\Person;

use CommonBundle\Entity\General\Address,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Barcode,
    CommonBundle\Test\Entity\User\AlwaysFailCredential,
    CommonBundle\Test\Entity\User\AlwaysPassCredential,
    CommonBundle\Test\Entity\User\Person\TestPerson;

class PersonTest extends \PHPUnit_Framework_TestCase
{
    public function testNameEmailAddressLanguage()
    {
        $person = new TestPerson();
        $person->setFirstName('Marco');
        $person->setLastName('Polo');
        $person->setEmail('mp@litus.cc');

        $address = new Address();
        $person->setAddress($address);

        $language = new Language('NoSQL', 'Not only SQL');
        $person->setLanguage($language);

        $this->assertEquals('Marco', $person->getFirstName());
        $this->assertEquals('Polo', $person->getLastName());
        $this->assertEquals('Marco Polo', $person->getFullName());
        $this->assertEquals('mp@litus.cc', $person->getEmail());

        $this->assertEquals($address, $person->getAddress());
        $this->assertEquals($language, $person->getLanguage());
    }

    public function testUsername()
    {
        $person = new TestPerson();
        $person->setUsername('username');
        $this->assertEquals('username', $person->getUsername());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNullUsername()
    {
        $person = new TestPerson();
        $person->setUsername(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoStringUserName()
    {
        $person = new TestPerson();
        $person->setUsername(array());
    }

    public function testHasCredentials()
    {
        $person = new TestPerson();
        $this->assertFalse($person->hasCredential());

        $person->setCredential(new AlwaysPassCredential());
        $this->assertTrue($person->hasCredential());
    }

    public function testValidationAlwaysFailCredentials()
    {
        $person = new TestPerson();
        $person->setCredential(new AlwaysFailCredential());
        $this->assertFalse($person->validateCredential(''));
    }

    public function testValidationAlwaysPassCredential()
    {
        $person = new TestPerson();
        $person->setCredential(new AlwaysPassCredential());
        $this->assertTrue($person->validateCredential(''));
    }

    public function testValidationNullCredential()
    {
        $person = new TestPerson();
        $this->assertFalse($person->validateCredential(''));
    }

    public function testSetPhoneNumber()
    {
        $person = new TestPerson();
        $person->setPhoneNumber('00 11');
        $this->assertEquals('0011', $person->getPhoneNumber());
    }

    public function testSetSexMale()
    {
        $person = new TestPerson();
        $person->setSex('m');
        $this->assertEquals('m', $person->getSex());
    }

    public function testSetSexFemale()
    {
        $person = new TestPerson();
        $person->setSex('f');
        $this->assertEquals('f', $person->getSex());
    }

    public function testSetSexNull()
    {
        $person = new TestPerson();
        $person->setSex(null);
        $this->assertNull($person->getSex());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetSexInCapitalLetter()
    {
        $person = new TestPerson();
        $person->setSex('M');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetSexInvalidLetter()
    {
        $person = new TestPerson();
        $person->setSex('X');
    }
}
