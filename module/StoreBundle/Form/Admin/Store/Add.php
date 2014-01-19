<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace StoreBundle\Form\Admin\Store;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a store
 *
 * @author Daan Wendelen <daanwendelen@gmail.com>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->add($tabContent);

        $field = new Text('name');
        $field->setLabel('Naam');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'store_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    )
                )
            )
        );

        return $inputFilter;
    }
}
