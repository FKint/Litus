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

namespace StoreBundle\Factory;

use StoreBundle\Entity\Storage;
use StoreBundle\Factory\StockCount\StorageCountFactory;
use StoreBundle\Factory\StockCount\UnitTypeCountFactory;
use StoreBundle\Factory\StockCount\ArticleCountFactory;
use StoreBundle\Entity\StockCount;
use StoreBundle\Factory\StockCount\BeginEndCountFactory;

/**
 * 
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class StockCountFactory
{
    public function createStockCount()
    {
        $f = new StorageCountFactory();
        $f = new BeginEndCountFactory($f);
        $f = new UnitTypeCountFactory($f);
        $f = new ArticleCountFactory($f);
        $sc = new StockCount($f);
        
        return $sc;
    }
}