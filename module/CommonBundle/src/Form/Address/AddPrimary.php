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

namespace CommonBundle\Form\Address;

use CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    Doctrine\ORM\EntityManager,
    Zend\Cache\Storage\StorageInterface as CacheStorage,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Address
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddPrimary extends \CommonBundle\Component\Form\Bootstrap\Element\Collection
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var \Zend\Cache\Storage\StorageInterface The cache instance
     */
    protected $_cache = null;

    /**
     * @var string
     */
    private $_prefix;

    /**
     * @param \Zend\Cache\Storage\StorageInterface $cache The cache instance
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $prefix
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(CacheStorage $cache, EntityManager $entityManager, $prefix = '', $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_cache = $cache;

        $prefix = '' == $prefix ? '' : $prefix . '_';
        $this->_prefix = $prefix;

        list($cities, $streets) = $this->_getCities();

        $field = new Select($prefix . 'address_city');
        $field->setLabel('City')
            ->setAttribute('class', $field->getAttribute('class') . ' input-large')
            ->setAttribute('options', $cities);
        $this->add($field);

        $field = new Text($prefix . 'address_postal_other');
        $field->setLabel('Postal Code')
            ->setAttribute('class', $field->getAttribute('class') . ' input-medium')
            ->setRequired();
        $this->add($field);

        $field = new Text($prefix . 'address_city_other');
        $field->setLabel('City')
            ->setAttribute('class', $field->getAttribute('class') . ' input-large')
            ->setRequired();
        $this->add($field);

        $field = new Text($prefix . 'address_street_other');
        $field->setLabel('Street')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $this->add($field);

        foreach($streets as $id => $collection) {
            $field = new Select($prefix . 'address_street_' . $id);
            $field->setLabel('Street')
                ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge ' . $prefix . 'address_street')
                ->setAttribute('options', $collection);
            $this->add($field);
        }

        $field = new Text($prefix . 'address_number');
        $field->setLabel('Number')
            ->setAttribute('class', $field->getAttribute('class') . ' input-medium')
            ->setRequired();
        $this->add($field);

        $field = new Text($prefix . 'address_mailbox');
        $field->setLabel('Mailbox')
            ->setAttribute('class', $field->getAttribute('class') . ' input-small')
            ->setRequired();
        $this->add($field);
    }

    private function _getCities()
    {
        if (null !== $this->_cache) {
            if (null !== ($result = $this->_cache->getItem('Litus_CommonBundle_Entity_General_Address_Cities_Streets'))) {
                return $result;
            }
        }

        $cities = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Address\City')
            ->findAll();

        $optionsCity = array(0 => '');
        $optionsStreet = array();
        foreach($cities as $city) {
            $optionsCity[$city->getId()] = $city->getPostal() . ' ' . $city->getName();
            $optionsStreet[$city->getId()] = array(0 => '');

            foreach($city->getStreets() as $street) {
                $optionsStreet[$city->getId()][$street->getId()] = $street->getName();
            }
        }
        $optionsCity['other'] = 'Other';

        if (null !== $this->_cache) {
            $this->_cache->setItem(
                'Litus_CommonBundle_Entity_General_Address_Cities_Streets',
                array(
                    $optionsCity,
                    $optionsStreet
                )
            );
        }

        return array($optionsCity, $optionsStreet);
    }

    public function getInputs()
    {
        $factory = new InputFactory();
        $inputs = array();

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_city',
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
        );

        /*if ($this->get($this->_prefix . 'address_city')->getValue() != 'other') {
            $inputs[] = $factory->createInput(
                array(
                    'name'     => $this->_prefix . 'address_street',
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
            );
        } else {
            $inputs[] = $factory->createInput(
                array(
                    'name'     => $this->_prefix . 'address_street_other',
                    'required' => true,
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
                    'name'     => $this->_prefix . 'address_postal_other',
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
                    'name'     => $this->_prefix . 'address_city_other',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'alpha',
                        ),
                    ),
                )
            );
        }*/

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_number',
                'required' => true,
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
                'name'     => $this->_prefix . 'address_mailbox',
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'alnum',
                        'options' => array(
                            'allowWhiteSpace' => true,
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                array(
                                    'max' => 5,
                                )
                            )
                        )
                    ),
                ),
            )
        );

        return $inputs;
    }
}
