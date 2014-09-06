<?php

namespace CommonBundle\Test\Entity;

use CommonBundle\Entity\Acl\Role;
use CommonBundle\Entity\Acl\Action;
use CommonBundle\Entity\Acl\Resource;
use CommonBundle\Component\Acl\Acl;

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

?>