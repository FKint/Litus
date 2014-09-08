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

use CommonBundle\Entity\General\Organization\Unit,
    CommonBundle\Entity\General\Organization;

class UnitTest extends \PHPUnit_Framework_TestCase
{

    public function testUnit()
    {
        $organisation = new Organization('Org');
        $unit1 = new Unit('unit1', 'unit1@Org', $organisation, array(
            1,
            2,
        ), array(
            100,
            101,
        ), true);

        $unit2 = new Unit('unit2', 'unit1@Org', $organisation, array(
            11,
            12,
        ), array(
            110,
            111,
        ), false);
        $unit21 = new Unit('unit21', 'unit21@Org', $organisation, array(
            1000,
            1102,
        ), array(
            1100,
            1001,
        ), true, $unit2);

        $this->assertEquals('unit1', $unit1->getName());
        $this->assertEquals('unit1@Org', $unit1->getMail());
        $this->assertEquals($organisation, $unit1->getOrganization());
        $this->assertTrue($unit1->getDisplayed());
        $this->assertTrue($unit1->isActive());

        $this->assertFalse($unit2->getDisplayed());

        $this->assertAllRoles($unit1, $unit2, $unit21);

        $unit1->deactivate();
        $this->assertFalse($unit1->isActive());

        $unit2->setDisplayed(true);
        $this->assertTrue($unit2->getDisplayed());

        $this->assertAllRoles($unit1, $unit2, $unit21);
    }

    private function assertAllRoles($unit1, $unit2, $unit21)
    {
        $this->assertRoles(array(
            100,
            101,
        ), $unit1->getCoordinatorRoles(true));
        $this->assertRoles(array(
            100,
            101,
        ), $unit1->getCoordinatorRoles(false));

        $this->assertRoles(array(
            110,
            111,
        ), $unit2->getCoordinatorRoles(true));
        $this->assertRoles(array(
            110,
            111,
        ), $unit2->getCoordinatorRoles(false));

        $this->assertRoles(array(
            110,
            111,
            1100,
            1001,
        ), $unit21->getCoordinatorRoles(true));
        $this->assertRoles(array(
            1100,
            1001,
        ), $unit21->getCoordinatorRoles(false));

        $this->assertRoles(array(
            1,
            2,
        ), $unit1->getRoles(true));
        $this->assertRoles(array(
            1,
            2,
        ), $unit1->getRoles(false));

        $this->assertRoles(array(
            11,
            12,
        ), $unit2->getRoles(true));
        $this->assertRoles(array(
            11,
            12,
        ), $unit2->getRoles(false));

        $this->assertRoles(array(
            11,
            12,
            1000,
            1102,
        ), $unit21->getRoles(true));
        $this->assertRoles(array(
            1000,
            1102,
        ), $unit21->getRoles(false));
    }

    private function assertRoles($expected, $roles)
    {
        sort($expected);
        sort($roles);

        $this->assertEquals($expected, $roles);
    }
}
