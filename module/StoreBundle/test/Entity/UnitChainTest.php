<?php
use StoreBundle\Entity\UnitChain;
use StoreBundle\Entity\UnitType;
use StoreBundle\Factory\UnitFactory;
use StoreBundle\test\Factory\TestUnitFactory;
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

class UnitChainTest extends PHPUnit_Framework_TestCase
{
	public function testSimple()
	{
		$uf = new TestUnitFactory();
		$us = new UnitChain();
		
		$fles = $uf->createCountUnitType("Flesje");
		$bak = $uf->createUnitType("Bak", $fles);
		$pallet = $uf->createUnitType("Pallet", $bak);
		
		$us->addUnitTypeToChain($fles, 1);
		$us->addUnitTypeToChain($bak, 24);
		$us->addUnitTypeToChain($pallet, 100);
		$this->assertEquals(2400, $us->getNbCountingUnitsInUnitType($pallet));
	}
	
    public function testGabs()
    {
		/*
		 *	Chain:		#subUnits
		 *
		 *	count		1
		 *	T1			10
		 *	T2			10
		 *	T3	T3Side	10
		 *	T4			10
		 */
    	
    	$uf = new TestUnitFactory();
    	$us = new UnitChain();
    	
    	$count = $uf->createCountUnitType("Count");
    	$t1 = $uf->createUnitType("t1", $count);
    	$t2 = $uf->createUnitType("t2", $t1);
    	$t3 = $uf->createUnitType("t3", $t2);
    	$t3side = $uf->createUnitType("t3side", $t2);
    	$t4 = $uf->createUnitType("t4", $t3);
    	
    	
    	$us->addUnitTypeToChain($t2, 10);
    	/*
    	 *	Chain:
    	 *	...
    	 *	T2 
    	 *	...
    	 */
    	$this->assertTrue($us->containsGab($t2));
    	$this->assertTrue($us->containsGab($t3));
    	
    	$this->assertTrue($us->canAddToChain($t4));
    	$this->assertTrue($us->canAddToChain($count));
    	
    	$us->addUnitTypeToChain($t1, 10);
    	/*
    	 *	Chain:
    	*	...
    	*	T1
    	*	T2
    	*	...
    	*/
    	$this->assertTrue($us->containsGab($t1));
    	
    	$us->addUnitTypeToChain($count, 1);
    	/*
    	 *	Chain:
    	*	count
    	*	T1
    	*	T2
    	*	...
    	*/
    	
    	$this->assertFalse($us->containsGab($t1));
    	$this->assertFalse($us->containsGab($t2));
    	$this->assertTrue($us->containsGab($t3));
    	$this->assertTrue($us->containsGab($t4));
    	$this->assertTrue($us->canAddToChain($t3side));
    	
    	$us->addUnitTypeToChain($t4, 10);
    	/*
    	 *	Chain:
    	*	count
    	*	T1
    	*	T2
    	*	...
    	*	T4
    	*/
    	$this->assertTrue($us->containsGab($t4));
    	$this->assertTrue($us->canAddToChain($t3side));
    	
    	$us->addUnitTypeToChain($t3, 10);
    	/*
    	 *	Chain:
    	*	count
    	*	T1
    	*	T2
    	*	T3
    	*	T4
    	*/
    	$this->assertFalse($us->containsGab($t3));
    	$this->assertFalse($us->containsGab($t4));
    	
    	$this->assertTrue($us->canAddToChain($t3side));
    	
    	$this->assertEquals(1000, $us->getNbCountingUnitsInUnitType($t3));
    }
}
