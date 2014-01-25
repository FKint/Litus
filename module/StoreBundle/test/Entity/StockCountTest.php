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
use StoreBundle\Factory\StoreFactory;
use StoreBundle\Factory\ArticleFactory;
use StoreBundle\Factory\StockCountFactory;
use StoreBundle\Factory\UnitFactory;
use StoreBundle\Factory\StorageFactory;
use StoreBundle\Entity\StockCountTuple;

class StockCountTest extends PHPUnit_Framework_TestCase
{
    public function testEditAndUse()
    {
        $af = new ArticleFactory();
        $uf = new UnitFactory();
        $sf = new StorageFactory();
        
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
        
        $fanta->getUnitChain()->addUnitTypeToChain($fles, 1);
        $fanta->getUnitChain()->addUnitTypeToChain($bak, 24);
        
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
         * | Kost           |   6.75    |   14.5    |
         * | Inkomsten      |   54      |   58      |
         * 
         * Inkomsten    = 112
         * Kost         = 21.25
         */
        
        #Begin cola
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($frigo);
        $t->setUnitType($fles);
        $t->setArticle($cola);
        $cf->setTupleValue($t, 5);
        
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($stock);
        $t->setUnitType($bak);
        $t->setArticle($cola);
        $cf->setTupleValue($t, 3);
        
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($stock);
        $t->setUnitType($fles);
        $t->setArticle($cola);
        $cf->setTupleValue($t, 2);
        
        #Begin fanta
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($frigo);
        $t->setUnitType($fles);
        $t->setArticle($fanta);
        $cf->setTupleValue($t, 10);
        
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($stock);
        $t->setUnitType($bak);
        $t->setArticle($fanta);
        $cf->setTupleValue($t, 1);
        
        $t = new StockCountTuple();
        $t->setBeginCount(true);
        $t->setStorage($stock);
        $t->setUnitType($fles);
        $t->setArticle($fanta);
        $cf->setTupleValue($t, 20);
        
        #Eind cola
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($frigo);
        $t->setUnitType($fles);
        $t->setArticle($cola);
        $cf->setTupleValue($t, 1);
        
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($stock);
        $t->setUnitType($bak);
        $t->setArticle($cola);
        $cf->setTupleValue($t, 1);
        
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($stock);
        $t->setUnitType($fles);
        $t->setArticle($cola);
        $cf->setTupleValue($t, 3);
        
        #Eind fanta
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($frigo);
        $t->setUnitType($fles);
        $t->setArticle($fanta);
        $cf->setTupleValue($t, 15);
        
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($stock);
        $t->setUnitType($bak);
        $t->setArticle($fanta);
        $cf->setTupleValue($t, 0);
        
        $t = new StockCountTuple();
        $t->setBeginCount(false);
        $t->setStorage($stock);
        $t->setUnitType($fles);
        $t->setArticle($fanta);
        $cf->setTupleValue($t, 10);
        
        $this->assertEquals(112, $cf->getIncome());
        $this->assertEquals(21.25, $cf->getCost());
    }
}
