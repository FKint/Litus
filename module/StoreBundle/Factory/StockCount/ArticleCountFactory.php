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

use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\StockCount\ArticleCount;

/**
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class ArticleCountFactory extends AmountCountFactory
{
    public function __construct($nextFactory)
    {
        parent::__construct($nextFactory);
    }
    
    public function create()
    {
        return new ArticleCount($this->getNextFactory());
    }
}