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

namespace StoreBundle\Component\StockCount;

use Doctrine\ORM\Mapping as ORM,
    StoreBundle\Component\StockCount\AbstractCount,
    StoreBundle\Component\Valuta\ValutaFactory;

/**
 * This class handles the articles
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class ArticleCount extends AbstractCount
{
    public function getIncome()
    {
        $t = (new ValutaFactory())->create0();

        foreach($this->getMap() as $k => $v) {
            $vt = $v->getAmount($k->getUnitChain());
            $t = $t->add($k->getSellingPrice()->multiply($vt));
        }

        return $t;
    }

    public function getCost()
    {
        $t = (new ValutaFactory())->create0();

        foreach($this->getMap() as $k => $v) {
            $vt = $v->getAmount($k->getUnitChain());
            $t = $t->add($k->getPurchasePricePortion()->multiply($vt));
        }

        return $t;
    }

    /**
     * (non-PHPdoc)
     * @see \StoreBundle\Entity\StockCount\AbstractCount::selectTupleItem()
     */
    protected function selectTupleItem($tuple)
    {
        return $tuple->getArticle();
    }
}