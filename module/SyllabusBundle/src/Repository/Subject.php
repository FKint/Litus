<?php

namespace SyllabusBundle\Repository;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr\Join;

/**
 * Subject
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Subject extends EntityRepository
{
    public function findAllByNameAndAcademicYearTypeAhead($name, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->where(
                $query->expr()->eq('m.academicYear', ':academicYear')
            )
            ->setParameter('academicYear', $academicYear->getId())
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach($resultSet as $subject)
            $ids[] = $subject->getSubject()->getId();

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->like($query->expr()->lower('s.name'), ':name'),
                        $query->expr()->like($query->expr()->lower('s.code'), ':name')
                    ),
                    $query->expr()->in('s.id', $ids)
                )
            )
            ->setParameter('name', strtolower(trim($name)) . '%')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
