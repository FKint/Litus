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

namespace LogisticsBundle\Form\Admin\VanReservation;

use LogisticsBundle\Entity\Reservation\VanReservation;

/**
 * The form used to add a new Reservation.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'LogisticsBundle\Hydrator\Reservation\VanReservation';

    /**
     * @var VanReservation|null
     */
    protected $reservation;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'datetime',
            'name'     => 'start_date',
            'label'    => 'Start Date',
            'required' => true,
        ));

        $this->add(array(
            'type'       => 'datetime',
            'name'       => 'end_date',
            'label'      => 'End Date',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'date_compare',
                            'options' => array(
                                'first_date' => 'start_date',
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                        array(
                            'name' => 'logistics_reservation_conflict',
                            'options' => array(
                                'start_date' => 'start_date',
                                'format' => 'd/m/Y H:i',
                                'resource' => PianoReservation::VAN_RESOURCE_NAME,
                                'reservation_id' => null === $this->reservation ? 0 : $this->reservation->getId(),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'reason',
            'label'    => 'Reason',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'    => 'text',
            'name'    => 'load',
            'label'   => 'Load',
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'    => 'textarea',
            'name'    => 'additional_info',
            'label'   => 'Additional Info',
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'driver',
            'label'      => 'Driver',
            'attributes' => array(
                'options' => $this->getDriversArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'passenger',
            'label'      => 'Passenger',
            'required'   => false,
            'options'    => array(
                'input' => array(
                    'validators'  => array(
                        array('name' => 'typeahead_person'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'reservation_add');

        if (null !== $this->reservation) {
            $this->bind($this->reservation);
        }
    }

    /**
     * @param  VanReservation $reservation
     * @return self
     */
    public function setReservation(VanReservation $reservation)
    {
        $this->reservation = $reservation;

        return $this;
    }

    private function getDriversArray()
    {
        $drivers = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findAllByYear($this->getCurrentAcademicYear());

        $driversArray = array(
            -1 => '',
        );
        foreach ($drivers as $driver) {
            $driversArray[$driver->getPerson()->getId()] = $driver->getPerson()->getFullName();
        }

        return $driversArray;
    }
}
