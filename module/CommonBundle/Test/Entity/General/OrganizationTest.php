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

use CommonBundle\Entity\General\Organization,
    CommonBundle\Entity\General\Organization\Unit;

class OrganizationTest extends \PHPUnit_Framework_TestCase
{
    public function testOrganisation()
    {
        $organisation = new Organization('Org');
        $this->assertEquals('Org', $organisation->getName());
    }

    public function testUnit()
    {
        $organisation = new Organization('Org');
        $unit1 = new Unit();
        $unit1->setName('unit1');
        $unit1->setMail('unit1@Org');
        $unit1->setOrganization($organisation);
        $unit1->setRoles(array(
            1,
            2,
        ));
        $unit1->setCoordinatorRoles(array(
            100,
            101,
        ));
        $unit1->setDisplayed(true);

        $unit2 = new Unit();
        $unit2->setName('unit2');
        $unit2->setMail('unit1@Org');
        $unit2->setOrganization($organisation);
        $unit2->setRoles(array(
            11,
            12,
        ));
        $unit2->setCoordinatorRoles(array(
            110,
            111,
        ));
        $unit2->setDisplayed(false);

        $unit21 = new Unit();
        $unit21->setName('unit21');
        $unit21->setMail('unit21@Org');
        $unit21->setOrganization($organisation);
        $unit21->setRoles(array(
            1000,
            1102,
        ));
        $unit21->setCoordinatorRoles(array(
            1100,
            1001,
        ));
        $unit21->setDisplayed(true);
        $unit21->setParent($unit2);

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
