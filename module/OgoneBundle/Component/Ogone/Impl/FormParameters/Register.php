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

namespace OgoneBundle\Component\Ogone\Impl\FormParameters;

use OgoneBundle\Component\Ogone\Order;
use OgoneBundle\Component\Ogone\Configuration;

/**
 * This class collects all the FormParameters.
 * 
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Register
{
    /**
     * @var \OgoneBundle\Component\Ogone\Impl\FormParameters\FormParameter[]
     */
    private $register;

    public function __construct()
    {
        $register = array();
        $register[] = new Address();
        $register[] = new Amount();
        $register[] = new Country();
        $register[] = new Currency();
        $register[] = new Description();
        $register[] = new Email();
        $register[] = new Language();
        $register[] = new Name();
        $register[] = new OrderId();
        $register[] = new Phonenumber();
        $register[] = new PSPId();
        $register[] = new Town();
        $register[] = new ZIP();
    }

    /**
     * Executes addToArrayIfValid on all form parameters.
     * 
     * @param \OgoneBundle\Component\Ogone\Order $order
     * @param \OgoneBundle\Component\Ogone\Configuration $configuration
     * 
     * @return array
     */
    public function createFormParametersIfValid(Order $order,
        Configuration $configuration)
    {
        $array = [];
 
        foreach($this->register as $r)
        {
            $r->addToArrayIfValid($array, $configuration, $order);
        }

        return $array;
    }
}
