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

namespace CudiBundle\Form\Prof\File;

use CommonBundle\Component\Form\Bootstrap\Element\File,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add File
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setAttribute('id', 'uploadFile');
        $this->setAttribute('enctype', 'multipart/form-data');

        $field = new Text('description');
        $field->setLabel('Description')
            ->setRequired();
        $this->add($field);

        $field = new File('file');
        $field->setLabel('File')
            ->setRequired();
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'description',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'file',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'filefilessize',
                            'options' => array(
                                'extension' => '256MB',
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
