<?php

namespace CommonBundle\Repository\General;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Organization
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Organization extends EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('o')
            ->from('CommonBundle\Entity\General\Organization', 'o')
            ->orderBy('o.name', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findOne()
    {
        if (count($this->findAll()) > 1)
            throw new \RuntimeException('There is more than one organization');

        return $this->findAll()[0];
    }
}
