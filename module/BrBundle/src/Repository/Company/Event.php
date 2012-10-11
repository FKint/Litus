<?php

namespace BrBundle\Repository\Company;

use BrBundle\Entity\Company as CompanyEntity,
    \DateTime,
    Doctrine\ORM\EntityRepository;

/**
 * Event
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Event extends EntityRepository
{
    public function findAllByCompany(CompanyEntity $company)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'c')
            ->where(
                $query->expr()->eq('e.company', ':company')
            )
            ->setParameter('company', $company->getId())
            ->orderBy('c.startDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllFuture(DateTime $date)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'c')
            ->where(
                $query->expr()->gte('c.startDate', ':date')
            )
            ->setParameter('date', $date)
            ->orderBy('c.startDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
