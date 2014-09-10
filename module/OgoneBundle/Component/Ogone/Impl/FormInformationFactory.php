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

namespace OgoneBundle\Component\Ogone\Impl;

/**
 * A factory to create FormInformation instances. Currently it only
 * creates SimpleFormInformation's, but it is easily refactored to an
 * abstract factory.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class FormInformationFactory
{
    /**
     * @param string $url
     * @param string $params
     *
     * @return \OgoneBundle\Component\Ogone\FormInformation
     */
    public function create($url, $params)
    {
        return new SimpleFormInformation($url, $params);
    }
}
