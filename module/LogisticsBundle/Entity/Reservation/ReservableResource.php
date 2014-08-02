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

namespace LogisticsBundle\Entity\Reservation;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * This is the entity for a driver.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Reservation\ReservableResource")
 * @ORM\Table(name="logistics.resources")
 */
class ReservableResource
{
    /**
     * @var string The name of this resource.
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @var ArrayCollection An array of \LogisticsBundle\Entity\Reservation\Reservation indicating when this resource is reserved.
     *
     * @ORM\OneToMany(targetEntity="LogisticsBundle\Entity\Reservation\Reservation", mappedBy="resource_id")
     */
    private $reservations;

    /**
     * Creates a new reservable resource.
     *
     * @param string $name The name of the resource.
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->reservations = new ArrayCollection();
    }

    /**
     * @return string The name of the resource.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array An array of \LogisticsBundle\Entity\Reservation indicating when this resource is reserved.
     */
    public function getReservations()
    {
        return $this->reservations->toArray();
    }

    /**
     * @param Reservation $reservation The reservation to add to this resource.
     */
    public function addReservation(Reservation $reservation)
    {
        $this->reservations->add($reservation);
    }

}
