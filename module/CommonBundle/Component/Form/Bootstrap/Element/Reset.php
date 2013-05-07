<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Form\Bootstrap\Element;

/**
 * Reset form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Reset extends \Zend\Form\Element\Submit
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'reset',
    );

    /**
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($name, $options = null)
    {
        parent::__construct($name, $options);
        $this->setAttribute('id', $name);
        $this->setAttribute('class', 'btn');
    }
}