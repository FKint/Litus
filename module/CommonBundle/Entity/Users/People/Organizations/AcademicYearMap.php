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

namespace CommonBundle\Entity\Users\People\Organizations;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Organization,
    CommonBundle\Entity\Users\People\Academic,
    Doctrine\ORM\Mapping as ORM;

/**
 * Specifying the mapping between organization and academic
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\Users\People\Organizations\AcademicYearMap")
 * @ORM\Table(
 *     name="users.people_organizations_academic_year_map",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="mapping_unique", columns={"academic", "academic_year"})}
 * )
 */
class AcademicYearMap
{
    /**
     * @var int The ID of this academic year map
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\Users\People\Academic The person
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\People\Academic", inversedBy="organizationMap")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The academic year
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var \CommonBundle\Entity\General\Organization The organization
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Organization")
     * @ORM\JoinColumn(name="organization", referencedColumnName="id")
     */
    private $organization;

    /**
     * @param \CommonBundle\Entity\Users\People\Academic $person The person
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The academic year
     * @param \CommonBundle\Entity\General\Organization $organization The organization
     */
    public function __construct(Academic $academic, AcademicYear $academicYear, Organization $organization)
    {
        $this->academic = $academic;
        $this->academicYear = $academicYear;
        $this->organization = $organization;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return \CommonBundle\Entity\General\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param \CommonBundle\Entity\General\Organization $organization
     * @return \CommonBundle\Entity\Users\People\Organizations\AcademicYearMap
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }
}