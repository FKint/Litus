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

namespace TicketBundle\Entity;

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM,
    InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Ticket")
 * @ORM\Table(
 *     name="tickets.tickets",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="ticket_number_unique", columns={"event", "number"})
 *      }
 * )
 */
class Ticket
{
    /**
     * @var array The possible states of a ticket
     */
    public static $possibleStatuses = array(
        'empty' => 'Empty',
        'booked' => 'Booked',
        'sold' => 'Sold',
    );

    /**
     * @var integer The ID of the ticket
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Event The event of the ticket
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\Event", inversedBy="tickets")
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var Person|null The person who bought/reserved the ticket
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var GuestInfo|null The guest info of who bought/reserved the ticket
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\GuestInfo")
     * @ORM\JoinColumn(name="guest_info", referencedColumnName="id")
     */
    private $guestInfo;

    /**
     * @var DateTime|null The date the ticket was booked
     *
     * @ORM\Column(name="book_date", type="datetime", nullable=true)
     */
    private $bookDate;

    /**
     * @var DateTime|null The date the ticket was sold
     *
     * @ORM\Column(name="sold_date", type="datetime", nullable=true)
     */
    private $soldDate;

    /**
     * @var integer|null The number of the ticket (unique for an event)
     *
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $number;

    /**
     * @var Option|null The option of the ticket
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\Option")
     * @ORM\JoinColumn(name="option", referencedColumnName="id")
     */
    private $option;

    /**
     * @var boolean|null Flag whether the ticket was sold to a member
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $member;

    /**
     * @param  Event                    $event
     * @param  string                   $status
     * @param  Person|null              $person
     * @param  GuestInfo|null           $guestInfo
     * @param  DateTime|null            $bookDate
     * @param  DateTime|null            $soldDate
     * @param  integer|null             $number
     * @throws InvalidArgumentException
     */
    public function __construct(Event $event, $status, Person $person = null, GuestInfo $guestInfo = null, DateTime $bookDate = null, DateTime $soldDate = null, $number = null)
    {
        if (!self::isValidTicketStatus($status)) {
            throw new InvalidArgumentException('The TicketStatus is not valid.');
        }

        $this->event = $event;
        $this->status = $status;
        $this->person = $person;
        $this->guestInfo = $guestInfo;
        $this->bookDate = $bookDate;
        $this->soldDate = $soldDate;
        $this->number = $number;
    }

    /**
     * @param  string  $status
     * @return boolean
     */
    public static function isValidTicketStatus($status)
    {
        return array_key_exists($status, self::$possibleStatuses);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return self::$possibleStatuses[$this->status];
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * @param  string                   $status
     * @return self
     * @throws InvalidArgumentException
     */
    public function setStatus($status)
    {
        if (!self::isValidTicketStatus($status)) {
            throw new InvalidArgumentException('The TicketStatus is not valid.');
        }

        if ($status == 'empty') {
            $this->person = null;
            $this->guestInfo = null;
            $this->bookDate = null;
            $this->soldDate = null;
        } elseif ($status == 'sold') {
            if ($this->bookDate == null) {
                $this->bookDate = new DateTime();
            }
            $this->soldDate = new DateTime();
        } elseif ($status == 'booked') {
            $this->bookDate = new DateTime();
            $this->soldDate = null;
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @return Person|null
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param  Person|null $person
     * @return self
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return GuestInfo|null
     */
    public function getGuestInfo()
    {
        return $this->guestInfo;
    }

    /**
     * @param  GuestInfo|null $guestInfo
     * @return self
     */
    public function setGuestInfo(GuestInfo $guestInfo = null)
    {
        $this->guestInfo = $guestInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if (null !== $this->person) {
            return $this->person->getFullName();
        }

        if (null !== $this->guestInfo) {
            return $this->guestInfo->getfullName();
        }

        return '';
    }

    /**
     * @return DateTime|null
     */
    public function getBookDate()
    {
        return $this->bookDate;
    }

    /**
     * @param  DateTime $bookDate
     * @return self
     */
    public function setBookDate(DateTime $bookDate)
    {
        $this->bookDate = $bookDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getSoldDate()
    {
        return $this->soldDate;
    }

    /**
     * @param  DateTime $soldDate
     * @return self
     */
    public function setSoldDate(DateTime $soldDate)
    {
        $this->soldDate = $soldDate;

        return $this;
    }

    /**
     * @return integer|null
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param  integer $number
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return Option|null
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param  Option|null $option
     * @return self
     */
    public function setOption(Option $option = null)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isMember()
    {
        return $this->member;
    }

    /**
     * @param  boolean $member
     * @return self
     */
    public function setMember($member)
    {
        $this->member = $member;

        return $this;
    }
}
