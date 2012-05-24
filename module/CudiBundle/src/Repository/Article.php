<?php

namespace CudiBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Article
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Article extends EntityRepository
{
    public function findAll()
    {
        $query = $this->_em->createQueryBuilder();
		$resultSet = $query->select('a')
			->from('CudiBundle\Entity\Article', 'a')
			->where(
			    $query->expr()->andX(
			        $query->expr()->eq('a.isHistory', 'false'),
			        $query->expr()->eq('a.isProf', 'false')
			    )
			)
			->getQuery()
			->getResult();

        return $resultSet;
    }
}