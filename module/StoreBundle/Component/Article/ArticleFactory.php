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

namespace StoreBundle\Component\Article;

use StoreBundle\Entity\Storage,
    StoreBundle\Entity\Article\Article,
    StoreBundle\Entity\Unit\UnitChain;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class ArticleFactory
{
    public function createArticle($name)
    {
        $a = new Article();
        $uc = new UnitChain();
        $a->setUnitChain($uc);
        $a->setName($name);

        return $a;
    }
}