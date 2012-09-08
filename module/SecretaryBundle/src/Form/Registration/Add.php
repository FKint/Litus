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

namespace SecretaryBundle\Form\Registration;

use Doctrine\ORM\EntityManager,
    CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
    CommonBundle\Component\Form\Bootstrap\Element\Collection,
    CommonBundle\Component\Form\Bootstrap\Element\File,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Validator\PhoneNumber as PhonenumberValidator,
    CommonBundle\Form\Address\Add as AddressForm,
    CommonBundle\Form\Address\AddPrimary as PrimaryAddressForm,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $identification The university identification
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $identification, $name = null)
    {
        parent::__construct($name);

        $this->setAttribute('id', 'register_form');

        $this->_entityManager = $entityManager;

        $this->setAttribute('enctype', 'multipart/form-data');

        $personal = new Collection('personal');
        $personal->setLabel('Personal');
        $this->add($personal);

        $field = new Text('first_name');
        $field->setLabel('First Name')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $personal->add($field);

        $field = new Text('last_name');
        $field->setLabel('Last Name')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $personal->add($field);

        $field = new Text('birthday');
        $field->setLabel('Birthday')
            ->setAttribute('placeholder', 'dd/mm/yyyy')
            ->setAttribute('class', $field->getAttribute('class') . ' input-large')
            ->setRequired();
        $personal->add($field);

        $field = new Select('sex');
        $field->setLabel('Sex')
            ->setAttribute('class', $field->getAttribute('class') . ' input-small')
            ->setAttribute(
                'options',
                array(
                    'm' => 'M',
                    'f' => 'F'
                )
            );
        $personal->add($field);

        $field = new File('profile');
        $field->setLabel('Profile Image');
        $personal->add($field);

        $field = new Text('phone_number');
        $field->setLabel('Phone Number')
            ->setAttribute('placeholder', '+CCAAANNNNNN');
        $personal->add($field);

        $field = new Text('university_identification');
        $field->setLabel('University Identification')
            ->setAttribute('class', $field->getAttribute('class') . ' input-large')
            ->setAttribute('disabled', true)
            ->setValue($identification);
        $personal->add($field);

        $field = new PrimaryAddressForm($entityManager, 'primary_address', 'primary_address');
        $field->setLabel('Primary Address');
        $this->add($field);

        $field = new AddressForm('secondary_address', 'secondary_address');
        $field->setLabel('Secondary Address');
        $this->add($field);

        $internet = new Collection('internet');
        $internet->setLabel('Internet');
        $this->add($internet);

        $field = new Text('university_email');
        $field->setLabel('University E-mail')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $internet->add($field);

        $field = new Text('personal_email');
        $field->setLabel('Personal E-mail')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $internet->add($field);

        $field = new Checkbox('primary_email');
        $field->setLabel('I want to receive VTK e-mail at my personal e-mail address')
            ->setValue(true);
        $internet->add($field);

        $organisation = new Collection('organisation');
        $organisation->setLabel('Organisation')
            ->setAttribute('id', 'organisation_info');
        $this->add($organisation);

        $field = new Checkbox('become_member');
        $field->setLabel('I want to become a member of the organisation');
        $organisation->add($field);

        $field = new Checkbox('conditions');
        $field->setLabel('I have read and agree with the terms and conditions');
        $organisation->add($field);

        $field = new Checkbox('irreeel');
        $field->setLabel('I want to receive my Ir.Reëel at CuDi')
            ->setValue(true);
        $organisation->add($field);

        $field = new Checkbox('bakske');
        $field->setLabel('I want to receive \'t Bakske by e-mail')
            ->setValue(false);
        $organisation->add($field);

        $field = new Select('tshirt');
        $field->setLabel('T-shirt Size')
            ->setAttribute('class', $field->getAttribute('class') . ' input-small')
            ->setAttribute(
                'options',
                array(
                    'XS' => 'XS',
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',
                    'XXL' => 'XXL',
                )
            );
        $organisation->add($field);

        $field = new Submit('register');
        $field->setValue('Register')
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();

            $inputs = $this->get('secondary_address')
                ->getInputs();
            foreach($inputs as $input)
                $inputFilter->add($input);

            $inputs =$this->get('primary_address')
                    ->getInputs();
            foreach($inputs as $input)
                $inputFilter->add($input);

            $factory = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'first_name',
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
                        'name'     => 'last_name',
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
                        'name'     => 'birthday',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y',
                                ),
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'sex',
                        'required' => true,
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'profile',
                        'required' => false,
                        'validators' => array(
                            array(
                                'name' => 'fileextension',
                                'options' => array(
                                    'extension' => 'jpg,png',
                                ),
                            ),
                            array(
                                'name' => 'filefilessize',
                                'options' => array(
                                    'extension' => '2MB',
                                ),
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'phone_number',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new PhoneNumberValidator(),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'university_email',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'EmailAddress',
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'personal_email',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'EmailAddress',
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'conditions',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'notempty',
                                'options' => array(
                                    'type' => 16,
                                ),
                            ),
                        ),
                    )
                )
            );

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}