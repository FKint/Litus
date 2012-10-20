<?php

namespace PublicationBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Publication
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Publication extends EntityRepository
{

    public function findOneActiveById($id)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PublicationBundle\Entity\Publication', 'p')
            ->where(
        		$query->expr()->andX(
                	$query->expr()->eq('p.id', ':id'),
                	$query->expr()->eq('p.deleted', 'false')
            	)
            )
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];
        return null;
    }

	public function findOneByTitle($title)
	{
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PublicationBundle\Entity\Publication', 'p')
            ->where(
                	$query->expr()->eq('p.title', ':title')
            )
            ->setParameter('title', $title)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];
        return null;
    }

	public function findAllActive()
	{
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PublicationBundle\Entity\Publication', 'p')
            ->where(
            	$query->expr()->eq('p.deleted', 'false')
            )
            ->orderBy('p.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

}
