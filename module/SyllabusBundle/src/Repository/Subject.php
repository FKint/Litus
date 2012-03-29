<?php

namespace SyllabusBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Subject
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Subject extends EntityRepository
{
    public function findAllByName($name)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
        	->from('SyllabusBundle\Entity\Subject', 's')
        	->where(
        	    $query->expr()->andX(
        	        $query->expr()->eq('s.active', 'true'),
                    $query->expr()->like($query->expr()->lower('s.name'), ':name')
        	    )
        	)
        	->setParameter('name', '%' . strtolower($name) . '%')
        	->getQuery()
        	->getResult();
        
        return $resultSet;
    }
}