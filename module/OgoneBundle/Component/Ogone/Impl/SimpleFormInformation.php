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
 * A simple implementation.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class SimpleFormInformation implements FormInformation
{
    private $url;
    private $params;

    public function __construct($url, $params)
    {
        $this->url = $url;
        $this->params = $params;
    }

    public function getActionUrl()
    {
        return $this->url;
    }

    public function getHiddenParameters()
    {
        return $this->params;
    }
}
