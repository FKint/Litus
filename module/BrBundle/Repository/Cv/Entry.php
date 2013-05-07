<?php

namespace BrBundle\Repository\Cv;

use Doctrine\ORM\EntityRepository,
    CommonBundle\Entity\General\AcademicYear,
    SyllabusBundle\Entity\Group,
    SyllabusBundle\Entity\Study;

/**
 * Entry
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Entry extends EntityRepository
{
    public function findAllByAcademicYear(AcademicYear $year) {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('BrBundle\Entity\Cv\Entry', 'e')
            ->where(
                $query->expr()->eq('e.year', ':year')
            )
            ->setParameter('year', $year)
            ->orderBy('e.lastName', 'ASC')
            ->addOrderBy('e.firstName', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllUngroupedStudies() {
        $query = $this->_em->createQueryBuilder();
        $subQuery = $this->_em->createQueryBuilder();

        $subQuery->select('e')
            ->from('BrBundle\Entity\Cv\Entry', 'e')
            ->where(
                $subQuery->expr()->eq('e.study', 's')
            );

        $groupQuery = $this->_em->createQueryBuilder();

        $groupQuery->select('g')
            ->from('SyllabusBundle\Entity\StudyGroupMap', 'g')
            ->innerJoin('g.group', 'd')
            ->where(
                $groupQuery->expr()->andx(
                    $groupQuery->expr()->eq('g.study', 's'),
                    $groupQuery->expr()->eq('d.cvBook', 'true')
                )
            );

        $resultSet = $query->select('s')
            ->from('SyllabusBundle\Entity\Study', 's')
            ->where(
                $query->expr()->andx(
                    $query->expr()->exists(
                        $subQuery->getDql()
                    ),
                    $query->expr()->not(
                        $query->expr()->exists(
                            $groupQuery->getDql()
                        )
                    )
                )
            )
            ->orderBy('s.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByGroupAndAcademicYear(Group $group, AcademicYear $year) {
        $query = $this->_em->createQueryBuilder();

        $subQuery = $this->_em->createQueryBuilder();

        $subQuery->select('g')
            ->from('SyllabusBundle\Entity\StudyGroupMap', 'g')
            ->where(
                $subQuery->expr()->andx(
                    $subQuery->expr()->eq('g.study', 's'),
                    $subQuery->expr()->eq('g.group', ':group')
                )
            );

        $resultSet = $query->select('e')
            ->from('BrBundle\Entity\Cv\Entry', 'e')
            ->innerJoin('e.study', 's')
            ->where(
                $query->expr()->andx(
                    $query->expr()->exists($subQuery->getDql()),
                    $query->expr()->eq('e.year', ':year')
                )
            )
            ->setParameter('group', $group)
            ->setParameter('year', $year)
            ->orderBy('e.lastName', 'ASC')
            ->addOrderBy('e.firstName', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByStudyAndAcademicYear(Study $study, AcademicYear $year) {
        $query = $this->_em->createQueryBuilder();

        $resultSet = $query->select('e')
            ->from('BrBundle\Entity\Cv\Entry', 'e')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('e.study', ':study'),
                    $query->expr()->eq('e.year', ':year')
                )
            )
            ->setParameter('study', $study)
            ->setParameter('year', $year)
            ->orderBy('e.lastName', 'ASC')
            ->addOrderBy('e.firstName', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}