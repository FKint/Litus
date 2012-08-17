<?php

namespace CommonBundle\Repository\Users;

use Doctrine\ORM\EntityRepository;

/**
 * Barcode
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Barcode extends EntityRepository
{
    function findOneByBarcode($barcode)
    {
        if (!is_numeric($barcode))
            return null;

        if (strlen($barcode) == 13)
            $barcode = floor($barcode / 10);
        if (strlen($barcode) > 12)
            return null;

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CommonBundle\Entity\Users\Barcode', 'b')
            ->where($query->expr()->eq('b.barcode', ':barcode'))
            ->setParameter('barcode', $barcode)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
