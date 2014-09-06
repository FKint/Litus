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

namespace CommonBundle\Test\Entity;

use CommonBundle\Entity\Acl\Role,
    CommonBundle\Entity\Acl\Action,
    CommonBundle\Entity\Acl\Resource,
    CommonBundle\Component\Acl\Acl;

class AclTest extends \PHPUnit_Framework_TestCase
{
    public function testOwnRole()
    {
        $superman = new Role('Superman');

        $males = new Role('Males');
        $xxx = new Resource('Porn');
        $watchPorn = new Action('watch', $xxx);
        $readPorn = new Action('read', $xxx);

        $males->addAction($watchPorn);
        $superman->addAction($readPorn);

        $females = new Role('Females');
        $flair = new Resource('Flair');
        $readFlair = new Action('read', $flair);
        $watchFlair = new Action('watch', $flair);

        $females->addAction($readFlair);
        $superman->addAction($watchFlair);

        $acl = new Acl($entityManager);

        $this->assertTrue($males->isAllowed($acl, 'Porn', 'watch'));
        $this->assertFalse($females->isAllowed($acl, 'Porn', 'watch'));
        $this->assertFalse($superman->isAllowed($acl, 'Porn', 'watch'));

        $this->assertFalse($males->isAllowed($acl, 'Flair', 'read'));
        $this->assertTrue($females->isAllowed($acl, 'Flair', 'read'));
        $this->assertFalse($superman->isAllowed($acl, 'Flair', 'read'));

        $this->assertFalse($males->isAllowed($acl, 'Flair', 'watch'));
        $this->assertFalse($females->isAllowed($acl, 'Flair', 'watch'));
        $this->assertFalse($superman->isAllowed($acl, 'Flair', 'watch'));

        $this->assertFalse($males->isAllowed($acl, 'Porn', 'read'));
        $this->assertFalse($females->isAllowed($acl, 'Porn', 'read'));
        $this->assertFalse($superman->isAllowed($acl, 'Porn', 'read'));

        $this->fail('Untestable: Conceptual simple test would require to much mocks.');
    }

    public function testGrandParents()
    {
        $granddad = new Role('Granddad');
        $grandmom = new Role('Granny');

        $dad = new Role('Dad');
        $dad->setParents(array($granddad));
        $dad->setParents(array($grandmom));

        $baby = new Role('Baby');
        $baby->setParents(array($dad));

        $aFortune = new Resource('A fortune');

        $killForIt = new Action('Kill for it', $aFortune);

        $granddad->addAction($killForIt);

        $acl = new Acl($entityManager);

        $this->assertTrue($baby->isAllowed($acl, 'A fortune', 'Kill for it'));
        $this->assertTrue($dad->isAllowed($acl, 'A fortune', 'Kill for it'));
        $this->assertTrue($granddad->isAllowed($acl, 'A fortune', 'Kill for it'));
        $this->assertFalse($grandmom->isAllowed($acl, 'A fortune', 'Kill for it'));

        $this->fail('Untestable: Conceptual simple test would require to much mocks.');
    }
}