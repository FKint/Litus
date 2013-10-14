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

namespace FormBundle\Component\Validator;

use CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Field\TimeSlot as TimeSlotField;

/**
 * Matches the timeslot for occupation of user
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TimeSlot extends \Zend\Validator\AbstractValidator
{
    /**
     * @var string The error codes
     */
    const NOT_VALID = 'notValid';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'You have already a subscription at this time',
    );

    /**
     * @var \FormBundle\Entity\Field\TimeSlot
     */
    private $_timeSlot;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \CommonBundle\Entity\User\Person
     */
    private $_person;

    /**
     * Sets validator options
     *
     * @param \FormBundle\Entity\Field\TimeSlot
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CommonBundle\Entity\User\Person $person
     * @return void
     */
    public function __construct(TimeSlotField $timeSlot, EntityManager $entityManager, Person $person = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_timeSlot = $timeSlot;
        $this->_entityManager = $entityManager;
        $this->_person = $person;
    }

    /**
     * Returns true if and only if the end date is after the start date
     *
     * @param  mixed $value
     * @param  array $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if (isset($value) && $value && null !== $this->_person) {
            $occupation = $this->_entityManager
                ->getRepository('FormBundle\Entity\Field\TimeSlot')
                ->findOneOccupationByPersonAndTime($this->_person, $this->_timeSlot->getStartDate(), $this->_timeSlot->getEndDate());

            if (null !== $occupation && $occupation->getField()->getId() != $this->_timeSlot->getId()) {
                $this->error(self::NOT_VALID);
                return false;
            }
        }

        return true;
    }
}
