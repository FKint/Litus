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

namespace StoreBundle\Entity\StockCount;

use Doctrine\ORM\Mapping as ORM,
    StoreBundle\Factory\Valuta\ValutaFactory;

/**
 * This count handles the storages.
 *
 * @see \StoreBundle\Entity\StockCount\AmountCount
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class StorageCount extends AbstractCount implements AmountCount
{
    /**
     * @param @see \StoreBundle\Entity\StockCount\AmountCount $nextFactory
     */
    public function __construct($nextFactory)
    {
        parent::__construct($nextFactory);
    }

    /**
     * (non-PHPdoc)
     * @see \StoreBundle\Entity\StockCount\AbstractCount::selectTupleItem()
     */
    public function selectTupleItem($tuple)
    {
        return $tuple->getStorage();
    }

    /**
     * (non-PHPdoc)
     * @see \StoreBundle\Entity\StockCount\AmountCount::getAmount()
     */
    public function getAmount($unitChain)
    {
        $t = 0;

        foreach($this->getMap() as $k => $v)
        {
            $vt = $v->getAmount($unitChain);
            $t += $vt;
        }
        return $t;
    }
}