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

namespace BrBundle\Repository\Company;

use BrBundle\Entity\Company as CompanyEntity,
    CommonBundle\Component\Doctrine\ORM\EntityRepository,
    DateTime;

/**
 * Event
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Event extends EntityRepository
{
    /**
     * @param  CompanyEntity       $company
     * @return \Doctrine\ORM\Query
     */
    public function findAllByCompanyQuery(CompanyEntity $company)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('e, c')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'c')
            ->where(
                $query->expr()->eq('e.company', ':company')
            )
            ->setParameter('company', $company->getId())
            ->orderBy('c.startDate', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  DateTime            $date
     * @param  CompanyEntity       $company
     * @return \Doctrine\ORM\Query
     */
    public function findAllFutureByCompanyQuery(DateTime $date, CompanyEntity $company)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('e, c')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->orx(
                        $query->expr()->gte('c.endDate', ':date'),
                        $query->expr()->gte('c.startDate', ':date')
                    ),
                    $query->expr()->eq('e.company', ':company')
                )
            )
            ->setParameter('company', $company->getId())
            ->setParameter('date', $date)
            ->orderBy('c.startDate', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  DateTime            $date
     * @return \Doctrine\ORM\Query
     */
    public function findAllFutureQuery(DateTime $date)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('e, ev')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'ev')
            ->innerJoin('e.company', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->orx(
                        $query->expr()->gte('ev.endDate', ':date'),
                        $query->expr()->gte('ev.startDate', ':date')
                    ),
                    $query->expr()->eq('c.active', 'true')
                )
            )
            ->setParameter('date', $date)
            ->orderBy('ev.startDate', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  DateTime            $date
     * @param  string              $string
     * @return \Doctrine\ORM\Query
     */
    public function findAllFutureBySearchQuery(DateTime $date, $string)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('e, ev')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'ev')
            ->innerJoin('e.company', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->orx(
                        $query->expr()->gte('ev.endDate', ':date'),
                        $query->expr()->gte('ev.startDate', ':date')
                    ),
                    $query->expr()->eq('c.active', 'true'),
                    $query->expr()->like('LOWER(c.name)', ':name')
                )
            )
            ->setParameter('date', $date)
            ->setParameter('name', strtolower($string))
            ->orderBy('ev.startDate', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  int                            $id
     * @return \BrBundle\Entity\Company\Event
     */
    public function findOneActiveById($id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'ev')
            ->innerJoin('e.company', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->orx(
                        $query->expr()->gte('ev.endDate', ':date'),
                        $query->expr()->gte('ev.startDate', ':date')
                    ),
                    $query->expr()->eq('e.id', ':id'),
                    $query->expr()->eq('c.active', 'true')
                )
            )
            ->setParameter('date', new DateTime())
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }
}
