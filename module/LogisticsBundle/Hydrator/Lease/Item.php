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

namespace LogisticsBundle\Hydrator\Lease;

use LogisticsBundle\Entity\Lease\Item as ItemEntity;

class Item extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'barcode', 'additional_info');

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        return $this->stdExtract($object, self::$stdKeys);
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new ItemEntity();
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}
