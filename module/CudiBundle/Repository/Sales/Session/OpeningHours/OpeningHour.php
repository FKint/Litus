<?php

namespace CudiBundle\Repository\Sales\Session\OpeningHours;

use DateInterval,
    DateTime,
    Doctrine\ORM\EntityRepository;

/**
 * OpeningHour
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OpeningHour extends EntityRepository
{
    public function findAllActive()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('o')
            ->from('CudiBundle\Entity\Sales\Session\OpeningHours\OpeningHour', 'o')
            ->where(
                $query->expr()->gte('o.endDate', ':now')
            )
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();

       return $resultSet;
    }

    public function findAllOld()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('o')
            ->from('CudiBundle\Entity\Sales\Session\OpeningHours\OpeningHour', 'o')
            ->where(
                $query->expr()->lt('o.endDate', ':now')
            )
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();

       return $resultSet;
    }

    public function findCurrentWeek()
    {
        $start = new DateTime();
        $start->setTime(0, 0);
        if ($start->format('N') > 5)
            $start->add(new DateInterval('P' . (8 - $start->format('N')) .'D'));
        else
            $start->sub(new DateInterval('P' . ($start->format('N') - 1) .'D'));

        $end = clone $start;
        $end->add(new DateInterval('P7D'));

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('o')
            ->from('CudiBundle\Entity\Sales\Session\OpeningHours\OpeningHour', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('o.startDate', ':start'),
                    $query->expr()->lt('o.endDate', ':end')
                )
            )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('o.startDate')
            ->getQuery()
            ->getResult();

       return $resultSet;
    }

    public function findCurrent()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('o')
            ->from('CudiBundle\Entity\Sales\Session\OpeningHours\OpeningHour', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->lt('o.startDate', ':now'),
                    $query->expr()->gt('o.endDate', ':now')
                )
            )
            ->setParameter('now', new DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
