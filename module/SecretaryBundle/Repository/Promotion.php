<?php

namespace SecretaryBundle\Repository;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityRepository;

/**
 * Promotion
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Promotion extends EntityRepository
{
    public function findAllByName($name, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSetAcademic = $query->select('a')
            ->from('SecretaryBundle\Entity\Promotion\Academic', 'a')
            ->innerJoin('a.academic', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.academicYear', ':academicYear'),
                    $query->expr()->orX(
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('p.firstName', "' '")),
                                $query->expr()->lower('p.lastName')
                            ),
                            ':name'
                        ),
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('p.lastName', "' '")),
                                $query->expr()->lower('p.firstName')
                            ),
                            ':name'
                        )
                    )
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        $query = $this->_em->createQueryBuilder();
        $resultSetExternal = $query->select('e')
            ->from('SecretaryBundle\Entity\Promotion\External', 'e')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('e.academicYear', ':academicYear'),
                    $query->expr()->orX(
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('e.firstName', "' '")),
                                $query->expr()->lower('e.lastName')
                            ),
                            ':name'
                        ),
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('e.lastName', "' '")),
                                $query->expr()->lower('e.firstName')
                            ),
                            ':name'
                        )
                    )
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        $resultSet = array();
        foreach($resultSetAcademic as $academic)
            $resultSet[$academic->getId()] = $academic;

        foreach($resultSetExternal as $external)
            $resultSet[$external->getId()] = $external;

        ksort($resultSet);

        return $resultSet;
    }

    public function findAllByEmail($email, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSetAcademic = $query->select('a')
            ->from('SecretaryBundle\Entity\Promotion\Academic', 'a')
            ->innerJoin('a.academic', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.academicYear', ':academicYear'),
                    $query->expr()->like($query->expr()->lower('p.email'), ':email')
                )
            )
            ->setParameter('email', '%' . strtolower($email) . '%')
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        $query = $this->_em->createQueryBuilder();
        $resultSetExternal = $query->select('e')
            ->from('SecretaryBundle\Entity\Promotion\External', 'e')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('e.academicYear', ':academicYear'),
                    $query->expr()->like($query->expr()->lower('e.email'), ':email')
                )
            )
            ->setParameter('email', '%' . strtolower($email) . '%')
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        $resultSet = array();
        foreach($resultSetAcademic as $academic)
            $resultSet[$academic->getId()] = $academic;

        foreach($resultSetExternal as $external)
            $resultSet[$external->getId()] = $external;

        ksort($resultSet);

        return $resultSet;
    }
}
