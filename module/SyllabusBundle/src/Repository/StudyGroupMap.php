<?php

namespace SyllabusBundle\Repository;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityRepository,
    SyllabusBundle\Entity\Group as GroupEntity,
    SyllabusBundle\Entity\Study as StudyEntity;

/**
 * StudyGroupMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StudyGroupMap extends EntityRepository
{
    public function findAllByGroupAndAcademicYear(GroupEntity $group, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\StudyGroupMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.group', ':group'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('group', $group)
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findOneByStudyGroupAndAcademicYear(StudyEntity $study, GroupEntity $group, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\StudyGroupMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.group', ':group'),
                    $query->expr()->eq('m.academicYear', ':academicYear'),
                    $query->expr()->eq('m.study', ':study')
                )
            )
            ->setParameter('group', $group)
            ->setParameter('academicYear', $academicYear)
            ->setParameter('study', $study)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}