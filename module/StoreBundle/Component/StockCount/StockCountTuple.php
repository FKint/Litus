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

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class StockCountTuple
{
    private $storage;
    private $unitType;
    private $article;
    private $beginCount;
    private $value;
    
    public function __construct()
    {
    }

    public function getStorage()
    {
        return $this->storage;
    }

    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    public function getArticle()
    {
        return $this->article;
    }

    public function setArticle($article)
    {
        $this->article = $article;
    }

    public function isBeginCount()
    {
        return $this->beginCount;
    }

    public function setBeginCount($beginCount)
    {
        $this->beginCount = $beginCount;
    }

    public function getUnitType()
    {
        return $this->unitType;
    }

    public function setUnitType($unitType)
    {
        $this->unitType = $unitType;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
    }
}
