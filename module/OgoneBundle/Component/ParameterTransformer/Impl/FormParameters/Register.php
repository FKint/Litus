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

namespace OgoneBundle\Component\ParameterTransformer\Impl\FormParameters;

use OgoneBundle\Component\ParameterTransformer\Order,
    OgoneBundle\Component\ParameterTransformer\Configuration;

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
        $this->register = array();
        $this->register[] = new Address();
        $this->register[] = new Amount();
        $this->register[] = new Country();
        $this->register[] = new Currency();
        $this->register[] = new Description();
        $this->register[] = new Email();
        $this->register[] = new Language();
        $this->register[] = new Name();
        $this->register[] = new OrderId();
        $this->register[] = new PhoneNumber();
        $this->register[] = new PSPId();
        $this->register[] = new Town();
        $this->register[] = new ZIP();
    }

    /**
     * Executes addToArrayIfValid on all form parameters.
     *
     * @param \OgoneBundle\Component\Ogone\Order $order
     * @param \OgoneBundle\Component\Ogone\Configuration $configuration
     *
     * @return array
     *
     * @throws \InvalidArgumentException One of the values was of the wrong type,
     * too long, or it was null while the parameter is mandatory.
     */
    public function createFormParametersIfValid(
        Order $order,
        Configuration $configuration
    ) {
        $array = [];
 
        foreach($this->register as $r)
        {
            $r->addToArrayIfValid($array, $configuration, $order);
        }

        return $array;
    }
}
