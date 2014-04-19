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

namespace SecretaryBundle\Repository;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Organization,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Component\Doctrine\ORM\EntityRepository,
    DateTime;

/**
 * Registration
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Registration extends EntityRepository
{
    public function findOneByAcademicAndAcademicYear(Academic $academic, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('SecretaryBundle\Entity\Registration', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('r.academic', ':academic'),
                    $query->expr()->eq('r.academicYear', ':academicYear')
                )
            )
            ->setParameter('academic', $academic)
            ->setParameter('academicYear', $academicYear)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findAllByUniversityIdentification($universityIdentification, AcademicYear $academicYear, Organization $organization = null)
    {
        $ids = array(0);
        if ($organization !== null) {
            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('a.id')
                ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
                ->innerJoin('m.academic', 'a')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('m.organization', ':organization'),
                        $query->expr()->eq('m.academicYear', ':academicYear')
                    )
                )
                ->setParameter('organization', $organization)
                ->setParameter('academicYear', $academicYear)
                ->getQuery()
                ->getResult();

            foreach ($resultSet as $result) {
                $ids[] = $result['id'];
            }
        }

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('SecretaryBundle\Entity\Registration', 'r')
            ->innerJoin('r.academic', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('a.universityIdentification'), ':universityIdentification'),
                    $query->expr()->eq('r.academicYear', ':academicYear'),
                    $organization == null ? '1=1' : $query->expr()->in('a.id', $ids)
                )
            )
            ->setParameter('universityIdentification', '%'.strtolower($universityIdentification).'%')
            ->setParameter('academicYear', $academicYear)
            ->orderBy('r.timestamp', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByName($name, AcademicYear $academicYear, Organization $organization = null)
    {
        $ids = array(0);
        if ($organization !== null) {
            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('a.id')
                ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
                ->innerJoin('m.academic', 'a')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('m.organization', ':organization'),
                        $query->expr()->eq('m.academicYear', ':academicYear')
                    )
                )
                ->setParameter('organization', $organization)
                ->setParameter('academicYear', $academicYear)
                ->getQuery()
                ->getResult();

            foreach ($resultSet as $result) {
                $ids[] = $result['id'];
            }
        }

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('SecretaryBundle\Entity\Registration', 'r')
            ->innerJoin('r.academic', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('a.firstName', "' '")),
                                $query->expr()->lower('a.lastName')
                            ),
                            ':name'
                        ),
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('a.lastName', "' '")),
                                $query->expr()->lower('a.firstName')
                            ),
                            ':name'
                        )
                    ),
                    $query->expr()->eq('r.academicYear', ':academicYear'),
                    $organization == null ? '1=1' : $query->expr()->in('a.id', $ids)
                )
            )
            ->setParameter('name', '%'.strtolower($name).'%')
            ->setParameter('academicYear', $academicYear)
            ->orderBy('r.timestamp', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByBarcode($barcode, AcademicYear $academicYear, Organization $organization = null)
    {
        if (!is_numeric($barcode))
            return array();

        $ids = array(0);
        if ($organization !== null) {
            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('a.id')
                ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
                ->innerJoin('m.academic', 'a')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('m.organization', ':organization'),
                        $query->expr()->eq('m.academicYear', ':academicYear')
                    )
                )
                ->setParameter('organization', $organization)
                ->setParameter('academicYear', $academicYear)
                ->getQuery()
                ->getResult();

            foreach ($resultSet as $result) {
                $ids[] = $result['id'];
            }
        }

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CommonBundle\Entity\User\Barcode', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->concat('b.barcode', '\'\''), ':barcode'),
                    $organization == null ? '1=1' : $query->expr()->in('b.person', $ids)
                )
            )
            ->setParameter('barcode', '%'.$barcode.'%')
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach ($resultSet as $result) {
            $ids[] = $result->getPerson()->getId();
        }

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('SecretaryBundle\Entity\Registration', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('r.academic', $ids),
                    $query->expr()->eq('r.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->orderBy('r.timestamp', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllSince(DateTime $since)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('SecretaryBundle\Entity\Registration', 'r')
            ->where(
                $query->expr()->gte('r.timestamp', ':since')
            )
            ->setParameter('since', $since)
            ->orderBy('r.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
