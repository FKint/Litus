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

namespace SecretaryBundle\Form\Registration;

use Doctrine\ORM\EntityManager,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic,
    SecretaryBundle\Entity\Organization\MetaData,
    Zend\Cache\Storage\StorageInterface as CacheStorage;

/**
 * Edit Registration
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \CommonBundle\Entity\User\Person\Academic $academic The academic
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The academic year
     * @param \SecretaryBundle\Entity\Organization\MetaData $metaData The organization metadata
     * @param \Zend\Cache\Storage\StorageInterface $cache The cache instance
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $identification The university identification
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Academic $academic, AcademicYear $academicYear, MetaData $metaData = null, CacheStorage $cache, EntityManager $entityManager, $identification, $name = null)
    {
        parent::__construct($cache, $entityManager, $identification, $name);

        $this->remove('register');

        $field = new Submit('register');
        $field->setValue('Save')
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);

        $this->populateFromAcademic($academic, $academicYear, $metaData);
    }
}
