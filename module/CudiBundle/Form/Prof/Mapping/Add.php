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

namespace CudiBundle\Form\Prof\Mapping;

use CommonBundle\Component\OldForm\Bootstrap\Element\Checkbox,
    CommonBundle\Component\OldForm\Bootstrap\Element\Submit,
    CommonBundle\Component\OldForm\Bootstrap\Element\Text,
    Zend\Form\Element\Hidden,
    Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

/**
 * Add Mapping
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\OldForm\Bootstrap\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $field = new Hidden('article_id');
        $field->setAttribute('id', 'articleId');
        $this->add($field);

        $field = new Text('article');
        $field->setLabel('Article')
            ->setAttribute('id', 'articleSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('mandatory');
        $field->setLabel('Mandatory');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'article_id',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'article',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
