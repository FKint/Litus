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

namespace BrBundle\Entity;

use BrBundle\Entity\Company,
    BrBundle\Entity\Contract\Composition,
    BrBundle\Entity\Contract\Section,
    CommonBundle\Entity\User\Person,
    BrBundle\Entity\Product\Order,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a contract.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Contract")
 * @ORM\Table(name="br.contracts")
 */
class Contract
{
    /**
     * @var int The contract's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \BrBundle\Entity\Product\Order The contract accompanying this order
     *
     * @ORM\OneToOne(
     *      targetEntity="BrBundle\Entity\Product\Order"
     * )
     */
    private $order;

    /**
     * @var \DateTime The date and time when this contract was written
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var \CommonBundle\Entity\User\Person The author of this contract
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="author", referencedColumnName="id")
     */
    private $author;

    /**
     * @var \BrBundle\Entity\Company The company for which this contract is meant
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company")
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    private $company;

    /**
     * @var \BrBundle\Entity\Br\Contracts\Composition The sections this contract contains
     *
     * @ORM\OneToMany(
     *      targetEntity="BrBundle\Entity\Contract\Composition",
     *      mappedBy="contract",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $composition;

    /**
     * @var int The discount the company gets, in %.
     *
     * @ORM\Column(type="integer")
     */
    private $discount;

    /**
     * @var string The title of the contract
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var int The invoice number; -1 indicates that the contract hasn't been signed yet
     *
     * @ORM\Column(name="invoice_nb", type="integer")
     */
    private $invoiceNb;

    /**
     * @var int The contract number. A form of identification that means something to the human users.
     *
     * @ORM\Column(name="contract_nb", type="integer", unique=true)
     */
    private $contractNb;

    /**
     * @var bool True if the contract has been updated but the updated version has not been generated yet.
     *
     * @ORM\Column(type="boolean")
     */
    private $dirty;

    /**
     * @param \CommonBundle\Entity\User\Person $author   The author of this contract
     * @param \BrBundle\Entity\Contract        $company  The company for which this contract is meant
     * @param int                              $discount The discount associated with this contract
     * @param string                           $title    The title of the contract
     */
    public function __construct(Order $order, Person $author, Company $company, $discount, $title)
    {
        $this->setOrder($order);
        $this->setDate();
        $this->setAuthor($author);
        $this->setCompany($company);
        $this->setDiscount($discount);
        $this->setTitle($title);

        $this->setDirty();
        $this->setInvoiceNb();

        $this->composition = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return \DateTime
     */
    public function setOrder(Order $order)
    {
        return $this->order = $order;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setDate()
    {
        $this->date = new DateTime();

        return $this;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @throws \InvalidArgumentException
     * @param  \CommonBundle\Entity\User\Person $author
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setAuthor(Person $author)
    {
        if ($author === null)
            throw new \InvalidArgumentException('Author cannot be null');

        $this->author = $author;

        return $this;
    }

    /**
     * @return BrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @throws \InvalidArgumentException
     * @param  \BrBundle\Entity\Company     $company
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setCompany(Company $company)
    {
        if ($company === null)
            throw new \InvalidArgumentException('Company cannot be null');

        $this->company = $company;

        return $this;
    }

    /**
     * @return array
     */
    public function getComposition()
    {
        return $this->composition->toArray();
    }

    /**
     * @return \BrBundle\Entity\Br\Contract
     */
    public function resetComposition()
    {
        $this->composition->clear();

        return $this;
    }

    /**
     * @param  \BrBundle\Entity\Br\Contracts\Section $section  The section that should be added
     * @param  int                                   $position The position of this section
     * @return \BrBundle\Entity\Br\Contract
     */
    public function addSection(Section $section, $position)
    {
        $this->composition->add(
            new Composition($this, $section, $position)
        );

        return $this;
    }

    /**
     * @param  array                        $sections The array containing all sections that should be added; the array keys will be used as the position
     * @return \BrBundle\Entity\Br\Contract
     */
    public function addSections(array $sections)
    {
        foreach ($sections as $position => $section)
            $this->addSection($section, $position);

        return $this;
    }

    /**
     * @throws \InvalidArgumentException
     * @param  int                          $discount The discount, $discount >= 0 && $discount <= 100
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setDiscount($discount)
    {
        if (($discount < 0) || ($discount > 100))
            throw new \InvalidArgumentException('Invalid discount');

        $this->discount = $discount;

        return $this;
    }

    /**
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @throws \InvalidArgumentException
     * @param  string                       $title The title of the contract
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setTitle($title)
    {
        if (($title === null) || !is_string($title))
            throw new \InvalidArgumentException('Invalid title');

        $this->title = $title;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDirty()
    {
        return $this->dirty;
    }

    /**
     * @param  bool                         $dirty
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setDirty($dirty = true)
    {
        $this->dirty = ($dirty ? true : false);

        return $this;
    }

    /**
     * @return bool
     */
    public function isSigned()
    {
        return $this->getInvoiceNb() != -1;
    }

    /**
     * @return int
     */
    public function getInvoiceNb()
    {
        return $this->invoiceNb;
    }

    /**
     * @throws \InvalidArgumentException
     * @param  int                          $invoiceNb
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setInvoiceNb($invoiceNb = -1)
    {
        if (($invoiceNb === null) || !is_numeric($invoiceNb))
            throw new \InvalidArgumentException('Invalid invoice number: ' . $invoiceNb);

        $this->invoiceNb = $invoiceNb;

        return $this;
    }

    /**
     * @return int
     */
    public function getContractNb()
    {
        return $this->contractNb;
    }

    /**
     * @param  int                          $contractNb
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setContractNb($contractNb)
    {
        if(($contractNb === null) || !is_numeric($contractNb))
            throw new \InvalidArgumentException('Invalid contract number: ' . $contractNb);

        $this->contractNb = $contractNb;

        return $this;
    }
}