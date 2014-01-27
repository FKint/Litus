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

use StoreBundle\Entity\Store;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class StoreFactory
{
    /**
     * @throws \InvalidArgumentException
     *
     * @param string $name The name of the store
     *
     * @return StoreBundle\Entity\Store
     */
    public function createStore($name)
    {
        $store = new Store();
        $store->setName($name);

        return $store;
    }
}