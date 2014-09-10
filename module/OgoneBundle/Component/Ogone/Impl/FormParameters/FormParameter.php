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

use OgoneBundle\Component\Ogone\Configuration,
    OgoneBundle\Component\Ogone\Order;

/**
 * Represents a parameter that can be send to Ogone.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
abstract class FormParameter
{
    /**
     * The key of the parameter.
     *
     * @return string
     */
    abstract protected function getKey();

    /**
     * The maximum number of characters than Ogone allows.
     *
     * @return integer
     */
    abstract protected function getMaxLength();

    /**
     * Is it an optional parameter?
     *
     * @return boolean
     */
    abstract protected function isOptional();

    /**
     * Validates a value. Returns true iff $val has the correct type.
     *
     * @param mixed $val
     *
     * @return boolean
     */
    abstract protected function validateType($val);

    /**
     * Selects the value of the parameter either from $config
     * or from $order.
     *
     * @param \OgoneBundle\Component\Ogone\Configuration $config
     * @param \OgoneBundle\Component\Ogone\Order $order
     */
    abstract protected function select(Configuration $config, Order $order);

    /**
     * Selects and validates the value of the parameter. If it is valid,
     * it will be added to the array.
     *
     * @param array $array
     * @param \OgoneBundle\Component\Ogone\Configuration $config
     * @param \OgoneBundle\Component\Ogone\Order $order
     *
     * @throws \InvalidArgumentException The value was of the wrong type,
     * too long, or it was null while the parameter is mandatory.
     */
    public function addToArrayIfValid($array, Configuration $config, Order $order)
    {
        $e = $this->select($config, $order);
        if(null === $e)
        {
            if(!$this->isOptional())
                throw new \InvalidArgumentException($this->getKey() . ' is not optional');

            return;
        }

        if(!$this->validateType($e))
            throw new \InvalidArgumentException($this->getKey() .
                ' has a different type. ' . $e .' given');

        if(strlen($e) > $this->getMaxLength())
            throw new \InvalidArgumentException($this->getKey() . ' can only be ' .
                $this->getMaxLength() . ' characters long, ' . $e . ' is ' . 
                strlen($e) . ' characters long.');

        $array[$this->getKey()] = $e;
    }
}
