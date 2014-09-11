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

namespace OgoneBundle\Test\Component\OgoneProtocol;

use OgoneBundle\Component\OgoneProtocol\Configuration;

/**
 * All configs return null.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class TestEnvironmentConfiguration implements Configuration
{
    public function getPspId()
    {
        return null;
    }

    public function getShaInPassphrase()
    {
        return null;
    }

    public function getShaOutPassphrase()
    {
        return null;
    }

    public function getCurrencyCode()
    {
        return null;
    }

    public function isProductionEnvironment()
    {
        return false;
    }
}
