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

namespace CommonBundle\Component\Form\Admin\Element;

/**
 * Textarea form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Textarea extends \Zend\Form\Element\Textarea implements \CommonBundle\Component\Form\Admin\Element
{
    /**
     * @var boolean
     */
    private $_required = false;

    /**
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($name, $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('id', $name);
        $this->setRequired(false);
    }

    /**
     * Specifies whether this element is a required field.
     *
     * Also sets the HTML5 'required' attribute.
     *
     * @param boolean $flag
     * @return void
     */
    public function setRequired($flag = true)
    {
        $this->setAttribute('required', $flag);
        $this->setLabelAttributes(
            array(
                'class' => $flag ? 'required' : 'optional',
            )
        );
        $this->_required = $flag;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->_required;
    }
}