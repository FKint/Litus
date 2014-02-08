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

use StoreBundle\Entity\Store;
use StoreBundle\Component\StoreFactory;
use StoreBundle\Component\Article\ArticleFactory;
use StoreBundle\Component\StockCount\StockCountFactory;
use StoreBundle\Component\Unit\UnitFactory;
use StoreBundle\Component\StorageFactory;
use StoreBundle\Component\StockCount\StockCountTuple;
use StoreBundle\Component\Valuta\ValutaFactory;

class StockCountTest extends PHPUnit_Framework_TestCase
{
    public function testEditAndUse()
    {
        $af = new ArticleFactory();
        $uf = new UnitFactory();
        $sf = new StorageFactory();
        $vf = new ValutaFactory();
        
        $scf = new StockCountFactory();
        $cf = $scf->createStockCount();
        
        $frigo = $sf->createStorage("Frigo");
        $stock = $sf->createStorage("Stock");
        
        $cola = $af->createArticle("Cola");
        $fanta = $af->createArticle("Fanta");
        
        $fles = $uf->createNextChainedUnitType("Fles");
        $bak = $uf->createNextChainedUnitType("Bak");
        
        $cola->getUnitChain()->addUnitTypeToChain($fles, 4);
        $cola->getUnitChain()->addUnitTypeToChain($bak, 12);
        $cola->setPurchasePrice($vf->createIncl(1, 0), $fles);
        $cola->setSellingPrice($vf->createIncl(2, 0));
        
        $fanta->getUnitChain()->addUnitTypeToChain($fles, 1);
        $fanta->getUnitChain()->addUnitTypeToChain($bak, 24);
        $fanta->setPurchasePrice($vf->createIncl(0.5, 0), $fles);
        $fanta->setSellingPrice($vf->createIncl(2, 0));
        
        /*
         * Begintelling:
         * |        | Frigo | Bak   | Los   |
         * |Cola    |  5    |   3   |  2    |
         * |Fanta   |  10   |   1   |   20  |
         * 
         * Eindtelling:
         * |        | Frigo | Bak   | Los   |
         * |Cola    |  1    |   1   |  3    |
         * |Fanta   |  15   |   0   |   10  |
         * 
         * Delta telling:
         * |        | Frigo | Bak   | Los   |   Totaal
         * |Cola    |  4    |   2   |   -1  |   27
         * |Fanta   |  -5   |   1   |   10  |   29
         * 
         * |                |   Cola    |   Fanta   |
         * | Aankoopprijs   |   1       |   0.5     |
         * | Verkoopprijs   |   2       |   2       |
         * | # porties      |   4       |   1       |
         * | Verbruik       |   27      |   29      |
         * | Kost           |   27      |   14.5    |
         * | Inkomsten      |   54      |   58      |
         * 
         * Inkomsten    = 274
         * Kost         = 41.5
         */
        
        #Begin cola
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($frigo);
        $t->setUnitType($fles);
        $t->setArticle($cola);
        $t->setValue(5);
        $cf->addCountTuple($t);
        
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($stock);
        $t->setUnitType($bak);
        $t->setArticle($cola);
        $t->setValue(3);
        $cf->addCountTuple($t);
        
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($stock);
        $t->setUnitType($fles);
        $t->setArticle($cola);
        $t->setValue(2);
        $cf->addCountTuple($t);
        
        #Begin fanta
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($frigo);
        $t->setUnitType($fles);
        $t->setArticle($fanta);
        $t->setValue(10);
        $cf->addCountTuple($t);
        
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($stock);
        $t->setUnitType($bak);
        $t->setArticle($fanta);
        $t->setValue(1);
        $cf->addCountTuple($t);
        
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($stock);
        $t->setUnitType($fles);
        $t->setArticle($fanta);
        $t->setValue(20);
        $cf->addCountTuple($t);
        
        #Eind cola
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($frigo);
        $t->setUnitType($fles);
        $t->setArticle($cola);
        $t->setValue(1);
        $cf->addCountTuple($t);
        
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($stock);
        $t->setUnitType($bak);
        $t->setArticle($cola);
        $t->setValue(1);
        $cf->addCountTuple($t);
        
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($stock);
        $t->setUnitType($fles);
        $t->setArticle($cola);
        $t->setValue(3);
        $cf->addCountTuple($t);
        
        #Eind fanta
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($frigo);
        $t->setUnitType($fles);
        $t->setArticle($fanta);
        $t->setValue(15);
        $cf->addCountTuple($t);
        
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($stock);
        $t->setUnitType($bak);
        $t->setArticle($fanta);
        $t->setValue(0);
        $cf->addCountTuple($t);
        
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($stock);
        $t->setUnitType($fles);
        $t->setArticle($fanta);
        $t->setValue(10);
        $cf->addCountTuple($t);
        
        $this->assertEquals(274, $cf->getIncome()->getIncl());
        $this->assertEquals(41.5, $cf->getCost()->getIncl());
    }
}
