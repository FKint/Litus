<?php

namespace FormBundle\Repository;

use DateTime,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Entry
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Entry extends EntityRepository
{
    public function findAllByFieldQuery($field)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Entry', 'n')
            ->where(
                $query->expr()->eq('n.field', ':field')
            )
            ->setParameter('field', $field)
            ->getQuery();

        return $resultSet;
    }

    public function findAllByFormEntryQuery($formEntry)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Entry', 'n')
            ->innerJoin('n.field', 'f')
            ->where(
                $query->expr()->eq('n.formEntry', ':formEntry')
            )
            ->setParameter('formEntry', $formEntry)
            ->orderBy('f.order', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findOneByFormEntryAndField($formEntry, $field)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Entry', 'n')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('n.field', ':field'),
                    $query->expr()->eq('n.formEntry', ':form')
                )
            )
            ->setParameter('field', $field)
            ->setParameter('form', $formEntry)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }
}
