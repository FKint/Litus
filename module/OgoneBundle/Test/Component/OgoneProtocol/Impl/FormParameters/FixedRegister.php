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

namespace OgoneBundle\Test\Component\OgoneProtocol\Impl\FormParameters;

use OgoneBundle\Component\OgoneProtocol\Impl\FormParameters\Register;
use OgoneBundle\Component\OgoneProtocol\Order;
use OgoneBundle\Component\OgoneProtocol\Configuration;

/**
 * The class calculates the signature for the parameters that are to be send
 * to Ogone. The pass phrase needs to be configured at Ogone.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class FixedRegister extends Register
{
    /**
     * @var array
     */
    private $params;

    /**
     * @param array $params
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    public function createFormParametersIfValid(
        Order $order,
        Configuration $configuration
    ) {
        return $this->params;
    }
}
