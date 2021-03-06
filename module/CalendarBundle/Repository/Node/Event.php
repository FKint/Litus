<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CalendarBundle\Repository\Node;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    DateTime;

/**
 * Event
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Event extends EntityRepository
{
    public function findAllActiveQuery($nbResults = 15)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('e')
            ->from('CalendarBundle\Entity\Node\Event', 'e')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->gt('e.endDate', ':now'),
                        $query->expr()->gt('e.startDate', ':now')
                    ),
                    $query->expr()->eq('e.isHistory', 'false')
                )
            )
            ->orderBy('e.startDate', 'ASC')
            ->setParameter('now', new DateTime());

        if ($nbResults > 0) {
            $query->setMaxResults($nbResults);
        }

        $resultSet = $query->getQuery();

        return $resultSet;
    }

    public function findAllOldQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('CalendarBundle\Entity\Node\Event', 'e')
            ->where(
                $query->expr()->andX(
                    $query->expr()->lt('e.startDate', ':now'),
                    $query->expr()->eq('e.isHistory', 'false')
                )
            )
            ->orderBy('e.startDate', 'DESC')
            ->setParameter('now', new DateTime())
            ->getQuery();

        return $resultSet;
    }

    public function findAllBetweenQuery(DateTime $first, DateTime $last)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('CalendarBundle\Entity\Node\Event', 'e')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gte('e.startDate', ':first'),
                    $query->expr()->lt('e.startDate', ':last'),
                    $query->expr()->eq('e.isHistory', 'false')
                )
            )
            ->orderBy('e.startDate', 'ASC')
            ->setParameter('first', $first)
            ->setParameter('last', $last)
            ->getQuery();

        return $resultSet;
    }
}
