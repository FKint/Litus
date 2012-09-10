<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Form\Address;

use CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Entity\General\Address,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Address
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Element\Collection
{
    /**
     * @var string
     */
    private $_prefix;

    /**
     * @param string $prefix
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($prefix = '', $name = null)
    {
        parent::__construct($name);

        $prefix = '' == $prefix ? '' : $prefix . '_';
        $this->_prefix = $prefix;

        $field = new Text($prefix . 'address_street');
        $field->setLabel('Street')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $this->add($field);

        $field = new Text($prefix . 'address_number');
        $field->setLabel('Number')
            ->setAttribute('class', $field->getAttribute('class') . ' input-medium')
            ->setRequired()
            ->setAttribute('size', 5);
        $this->add($field);

        $field = new Text($prefix . 'address_postal');
        $field->setLabel('Postal Code')
            ->setAttribute('class', $field->getAttribute('class') . ' input-medium')
            ->setRequired()
            ->setAttribute('size', 10);
        $this->add($field);

        $field = new Text($prefix . 'address_city');
        $field->setLabel('City')
            ->setAttribute('class', $field->getAttribute('class') . ' input-large')
            ->setRequired();
        $this->add($field);

        $field = new Select($prefix . 'address_country');
        $field->setLabel('Country')
            ->setAttribute('options', $this->_getCountries());
        $this->add($field);

        $this->populateValues(
            array(
                $prefix . 'address_country' => 'BE'
            )
        );
    }

    private function _getCountries()
    {
        $options = array();
        foreach(Address::$countries as $key => $continent) {
            $options[$key] = array(
                'label' => $key,
                'options' => $continent,
            );
        }
        return $options;
    }

    public function getInputs()
    {
        $factory = new InputFactory();
        $inputs = array();

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_street',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'alpha',
                        'options' => array(
                            'allowWhiteSpace' => true,
                        ),
                    ),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_number',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'alnum',
                        'options' => array(
                            'allowWhiteSpace' => true,
                        ),
                    ),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_postal',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'digits',
                    ),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_city',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'alpha',
                    ),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_country',
                'required' => true,
            )
        );

        return $inputs;
    }
}
