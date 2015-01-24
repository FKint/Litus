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

use CommonBundle\Entity\User\Credential;

class CredentialTest extends \PHPUnit_Framework_TestCase
{
    public function testValidation()
    {
        $good = 'Bear';
        $bad = 'Raeb';
        $credential = new Credential($good);
        $this->assertTrue($credential->validateCredential($good));
        $this->assertFalse($credential->validateCredential($bad));
    }

    public function testShouldUpdateAllNew()
    {
        $good = 'Bear';
        $credential = new Credential($good);
        $this->assertFalse($credential->shouldUpdate());
    }

    public function testShouldUpdateOldAlgo()
    {
        $good = 'Bear';
        $credential = new Credential($good, 'md5');
        $this->assertTrue($credential->shouldUpdate());
    }

    public function testShouldUpdateOldNbIterations()
    {
        $good = 'Bear';
        $credential = new Credential($good, 'sha512', 1);
        $this->assertTrue($credential->shouldUpdate());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAlgorithm()
    {
        new Credential('pass', 'Non-Existing-Algo');
    }

    public function testInvalidNumberOfIterations()
    {
        $credential = new Credential('pass', 'sha512', 'a');
        $this->assertTrue($credential->validateCredential('pass'));
        $this->assertFalse($credential->validateCredential('fake'));
    }

    public function testZeroNumberOfIterations()
    {
        $credential = new Credential('pass', 'sha512', 0);
        $this->assertTrue($credential->validateCredential('pass'));
        $this->assertFalse($credential->validateCredential('fake'));
    }

    public function testNegativeNumberOfIterations()
    {
        $credential = new Credential('pass', 'sha512', -1);
        $this->assertTrue($credential->validateCredential('pass'));
        $this->assertFalse($credential->validateCredential('fake'));
    }
}
