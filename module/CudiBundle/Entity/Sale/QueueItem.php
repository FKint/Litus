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

namespace CudiBundle\Entity\Sale;

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\QueueItem")
 * @ORM\Table(name="cudi.sales_queue_items")
 */
class QueueItem
{
    /**
     * @var integer The ID of the queue item
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The person of the queue item
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var Session The session of the queue item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Session")
     * @ORM\JoinColumn(name="session", referencedColumnName="id")
     */
    private $session;

    /**
     * @var PayDesk The pay desk of the queue item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\PayDesk")
     * @ORM\JoinColumn(name="pay_desk", referencedColumnName="id")
     */
    private $payDesk;

    /**
     * @var integer The number of the queue item
     *
     * @ORM\Column(type="smallint", name="queue_number")
     */
    private $queueNumber;

    /**
     * @var string The status of the queue item
     *
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @var DateTime The time the queue item was created
     *
     * @ORM\Column(type="datetime", name="sign_in_time")
     */
    private $signInTime;

    /**
     * @var DateTime The time there were articles sold to the queue item
     *
     * @ORM\Column(type="datetime", name="sold_time", nullable=true)
     */
    private $soldTime;

    /**
     * @var string The comment of the queue item
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string The pay method of the queue item
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $payMethod;

    /**
     * @var ArrayCollection The sold items
     *
     * @ORM\OneToMany(targetEntity="CudiBundle\Entity\Sale\SaleItem", mappedBy="queueItem")
     */
    private $saleItems;

    /**
     * @var ArrayCollection The return items
     *
     * @ORM\OneToMany(targetEntity="CudiBundle\Entity\Sale\ReturnItem", mappedBy="queueItem")
     */
    private $returnItems;

    /**
     * @var boolean Flag whether collect ticket was already printed
     * @ORM\Column(name="collect_printed", type="boolean")
     */
    private $collectPrinted;

    /**
     * @var array The possible states of a queue item
     */
    private static $possibleStatuses = array(
        'signed_in' => 'Signed In',
        'collecting' => 'Collecting',
        'collected' => 'Collected',
        'selling' => 'Selling',
        'hold' => 'Hold',
        'canceled' => 'Canceled',
        'sold' => 'Sold',
    );

    /**
     * @var array The possible pay methods of a queue item
     */
    public static $possiblePayMethods = array(
        'cash' => 'Cash',
        'bank' => 'Bank Device',
    );

    /**
     * @param EntityManager $entityManager
     * @param Person        $person
     * @param Session       $session
     */
    public function __construct(EntityManager $entityManager, Person $person, Session $session)
    {
        $this->person = $person;
        $this->session = $session;
        $this->setStatus('signed_in');
        $this->collectPrinted = false;

        $this->queueNumber = $entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->getNextQueueNumber($session);
    }

    /**
     * @param  string  $status
     * @return boolean
     */
    public static function isValidQueueStatus($status)
    {
        return array_key_exists($status, self::$possibleStatuses);
    }

    /**
     * @param  string  $payMethod
     * @return boolean
     */
    public static function isValidPayMethod($payMethod)
    {
        return array_key_exists($payMethod, self::$possiblePayMethods);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param PayDesk $payDesk
     *
     * @return self
     */
    public function setPayDesk(PayDesk $payDesk)
    {
        $this->payDesk = $payDesk;

        return $this;
    }

    /**
     * @return PayDesk
     */
    public function getPayDesk()
    {
        return $this->payDesk;
    }

    /**
     * @return integer
     */
    public function getQueueNumber()
    {
        return $this->queueNumber;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusReadable()
    {
        return self::$possibleStatuses[$this->status];
    }

    /**
     * @param  string                   $status
     * @throws InvalidArgumentException
     * @return self
     */
    public function setStatus($status)
    {
        if (!self::isValidQueueStatus($status)) {
            throw new InvalidArgumentException('The queue status is not valid.');
        }

        $this->status = $status;

        if ($status != 'sold' && $status != 'selling') {
            $this->payDesk = null;
        }

        switch ($status) {
            case 'signed_in':
                $this->signInTime = new DateTime();
                break;
            case 'sold':
                $this->soldTime = new DateTime();
                break;
        }

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getSignInTime()
    {
        return $this->signInTime;
    }

    /**
     * @return DateTime
     */
    public function getSoldTime()
    {
        return $this->soldTime;
    }

    /**
     * @param string $comment
     *
     * @return self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param  string                   $payMethod
     * @throws InvalidArgumentException
     * @return self
     */
    public function setPayMethod($payMethod)
    {
        if (!self::isValidPayMethod($payMethod) && $payMethod !== null) {
            throw new InvalidArgumentException('The pay method is not valid.');
        }

        $this->payMethod = $payMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayMethod()
    {
        if (!self::isValidPayMethod($this->payMethod)) {
            return '';
        }

        return self::$possiblePayMethods[$this->payMethod];
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleItems()
    {
        return $this->saleItems;
    }

    /**
     * @return ArrayCollection
     */
    public function getReturnItems()
    {
        return $this->returnItems;
    }

    /**
     * @param boolean $collectPrinted
     *
     * @return self
     */
    public function setCollectPrinted($collectPrinted = true)
    {
        $this->collectPrinted = $collectPrinted;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCollectPrinted()
    {
        return $this->collectPrinted;
    }
}
