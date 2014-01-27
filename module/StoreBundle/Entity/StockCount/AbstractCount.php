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
    CommonBundle\Component\Map\DoctrineMap,
    StoreBundle\Entity\StockCountTuple;

/**
 * Reduces duplication in the implementations of AmountCount.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
abstract class AbstractCount
{
    /**
     * @param \StoreBundle\Factory\StockCount\AmountCountFactory $nextFactory
     */
    public function __construct($nextFactory)
    {
        $this->nextFactory = $nextFactory;
        $this->map = new DoctrineMap();
    }

    /**
     * @see \StoreBundle\Entity\StockCount\AmountCount::setTupleValue()
     */
    public function setTupleValue($tuple, $value)
    {
        $i = $this->selectTupleItem($tuple);

        $this->map->getOrCreate($i, $this->nextFactory)
            ->setTupleValue($tuple, $value);
    }

    /**
     * @var \StoreBundle\Factory\StockCount\AmountCountFactory
     */
    private $nextFactory;

    /**
     * Returns the element that this Count will handle from the tuple.
     *
     * @param \StoreBundle\Entity\StockCountTuple $tuple
     *
     * @return mixed
     */
    protected abstract function selectTupleItem($tuple);

    /**
     * The map that maps the element of the tuple to the next Count
     * in the chain.
     *
     * @return \CommonBundle\Component\Map\Map
     */
    protected function getMap()
    {
        return $this->map;
    }

    /**
     * The map that maps the element of the tuple to the next Count
     * in the chain.
     *
     * @var \CommonBundle\Component\Map\Map
     */
    private $map;
}