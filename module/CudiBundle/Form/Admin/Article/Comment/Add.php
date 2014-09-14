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

namespace CudiBundle\Form\Admin\Article\Comment;

use CommonBundle\Component\OldForm\Admin\Element\Select,
    CommonBundle\Component\OldForm\Admin\Element\Textarea,
    CudiBundle\Entity\Comment\Comment,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Comment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\OldForm\Admin\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $field = new Textarea('text');
        $field->setLabel('Comment')
            ->setRequired();
        $this->add($field);

        $field = new Select('type');
        $field->setLabel('Type')
            ->setAttribute('options', Comment::$POSSIBLE_TYPES)
            ->setAttribute('data-help', 'The comment type defines the visibility of the comment:
            <ul>
                <li><b>Internal:</b> These comments will only be visible in the admin</li>
                <li><b>External:</b> These comments will only be visible in \'Prof App\' and in the admin</li>
                <li><b>Site:</b> These comments will also be visible on the website</li>
            </ul>')
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'comment_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'text',
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
                    'name'     => 'type',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}
