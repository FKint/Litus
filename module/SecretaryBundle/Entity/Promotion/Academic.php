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

namespace SecretaryBundle\Entity\Promotion;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic as AcademicPerson,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a promotion.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Promotion\Academic")
 * @ORM\Table(name="general.promotions_academic")
 */
class Academic extends \SecretaryBundle\Entity\Promotion
{
    /**
     * @var AcademicPerson The academic associated with this entry.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id", nullable=false)
     */
    private $academic;

    /**
     * Creates a new promotion with the given academic.
     *
     * @param AcademicYear   $academicYear The academic year for this promotion.
     * @param AcademicPerson $academic     The academic to add.
     */
    public function __construct(AcademicYear $academicYear, AcademicPerson $academic)
    {
        parent::__construct($academicYear);

        $this->academic = $academic;
    }

    /**
     * @return AcademicPerson
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->academic->getEmail();
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->academic->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->academic->getLastName();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->academic->getFullName();
    }
}
