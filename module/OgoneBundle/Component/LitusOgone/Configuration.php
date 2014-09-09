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

namespace \OgoneBundle\Component\LitusOgone;

use \CommonBundle\Repository\General\Config;

class Configuration implements \OgoneBundle\Component\Ogone\Configuration
{
    /**
     * @var \CommonBundle\Repository\General\Config
     */
    private $config;

    public function __construct(Config $config)
    {
        return $this->config = $config;
    }

    public function getPspId()
    {
        return $this->config->getConfigValue('ogone.pspid');
    }

    public function getShaInPassphrase()
    {
        return $this->config->getConfigValue('ogone.sha-in');
    }

    public function getShaOutPassphrase()
    {
        return $this->config->getConfigValue('ogone.sha-out');
    }

    public function getCurrencyCode()
    {
        return $this->config->getConfigValue('ogone.currency');
    }

    public function isProductionEnvironment()
    {
        return 'production' == getenv('APPLICATION_ENV');
    }
}
