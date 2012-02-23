<?php

namespace CudiBundle\Repository\Sales;

use CommonBundle\Entity\General\Bank\CashRegister,
	Doctrine\ORM\EntityRepository;

/**
 * Session
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Session extends EntityRepository
{

	public function findOneByCashRegister(CashRegister $cashRegister)
	{
		$query = $this->_em->createQueryBuilder();
		$resultSet = $query->select('s')
			->from('CudiBundle\Entity\Sales\Session', 's')
			->where($query->expr()->orX(
					$query->expr()->eq('s.openAmount', ':register'),
					$query->expr()->eq('s.closeAmount', ':register')
				)
			)
			->setParameter('register', $cashRegister->getId())
			->setMaxResults(1)
			->getQuery()
			->getResult();
		
		if (isset($resultSet[0]))
			return $resultSet[0];
		
		return null;
	}
	
	public function findOpenSession()
	{
		$query = $this->_em->createQueryBuilder();
		$resultSet = $query->select('s')
			->from('CudiBundle\Entity\Sales\Session', 's')
			->where($query->expr()->isNull('s.closeDate'))
			->orderBy('s.openDate', 'ASC')
			->setMaxResults(1)
			->getQuery()
			->getResult();
		
		if (isset($resultSet[0]))
			return $resultSet[0];
		
		return null;
	}
}