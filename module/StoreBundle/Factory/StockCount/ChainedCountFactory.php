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

namespace StoreBundle\Factory\StockCount;

use Doctrine\ORM\Mapping as ORM,
    CommonBundle\Component\Map\MapFactory,
    StoreBundle\Entity\StockCount\AmountCount;

/**
 * Factories extending this class will all create a link that is not the end
 * of the chain.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
abstract class ChainedCountFactory implements AmountCountFactory
{
    /**
     * @param \StoreBundle\Factory\StockCount\AmountCountFactory $nextFactory
     */
    public function __construct($nextFactory)
    {
        $this->nextFactory = $nextFactory;
    }

    /**
     * @return \StoreBundle\Factory\StockCount\AmountCountFactory
     */
    protected function getNextFactory()
    {
        return $this->nextFactory;
    }

    /**
     * @var \StoreBundle\Factory\StockCount\AmountCountFactory
     */
    private $nextFactory;

    /**
     * (non-PHPdoc)
     * @see \CommonBundle\Component\Map\MapFactory::create()
     *
     * @return  \StoreBundle\Entity\StockCount\AmountCount
     */
    public abstract function create();
}