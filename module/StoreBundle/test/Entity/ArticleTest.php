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

use StoreBundle\Entity\Storage;
use StoreBundle\Factory\StorageFactory;
use StoreBundle\Factory\ArticleFactory;
use StoreBundle\Factory\UnitFactory;
use StoreBundle\Factory\Valuta\ValutaFactory;
use StoreBundle\test\Factory\TestUnitFactory;

class ArticleTest extends PHPUnit_Framework_TestCase
{
    public function testStella()
    {
        $uf = new UnitFactory();        
        $af = new ArticleFactory();
        $vf = new ValutaFactory();
        
        $s = $af->createArticle("Stella");
        
        $vat = $uf->createNextChainedUnitType("Vat");
        $pallet = $uf->createNextChainedUnitType("Pallet");
        
        $s->getUnitChain()->addUnitTypeToChain($vat, 200);
        $s->getUnitChain()->addUnitTypeToChain($pallet, 8);
        
        $s->setPurchasePrice($vf->createExcl(800, 0.21), $pallet);
        
        $this->assertEquals(0.5, $s->getPurchasePricePortion()->getExcl());
        $this->assertEquals(100, $s->getPurchasePrice($vat)->getExcl());
        $this->assertEquals(800, $s->getPurchasePrice($pallet)->getExcl());
        
        $s->setSellingPrice($vf->createIncl(1.21, 0.21));
        
        $this->assertEquals(1.21, $s->getSellingPrice()->getIncl());
        
        $this->assertEquals(0.5, $s->getMarginPerPortion()->getExcl());
    }
}
