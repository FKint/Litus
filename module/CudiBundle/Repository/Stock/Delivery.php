<?php

namespace CudiBundle\Repository\Stock;

use CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Stock\Period as PeriodEntity,
    CudiBundle\Entity\Supplier,
    DateTime,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Delivery
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Delivery extends EntityRepository
{
    public function findAllBySupplierAndPeriodQuery(Supplier $supplier, PeriodEntity $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('o')
            ->from('CudiBundle\Entity\Stock\Delivery', 'o')
            ->innerJoin('o.article', 'a', Join::WITH,
                   $query->expr()->eq('a.supplier', ':supplier')
            )
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('o.timestamp', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('o.timestamp', ':endDate')
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->setParameter('startDate', $period->getStartDate())
            ->orderBy('o.timestamp', 'DESC');

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery();

        return $resultSet;
    }

    public function findAllByPeriodQuery(PeriodEntity $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('o')
            ->from('CudiBundle\Entity\Stock\Delivery', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('o.timestamp', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('o.timestamp', ':endDate')
                )
            )
            ->setParameter('startDate', $period->getStartDate())
            ->orderBy('o.timestamp', 'DESC');

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery();

        return $resultSet;
    }

    public function findNumberByArticleAndAcademicYear(Article $article, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('SUM(d.number)')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('d.article', ':article'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('article', $article)
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->getQuery()
            ->getSingleScalarResult();

        if (null == $resultSet)
            return 0;

        return $resultSet;
    }

    public function findNumberBySupplier(Supplier $supplier, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('SUM(d.number)')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('supplier', $supplier)
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->getQuery()
            ->getSingleScalarResult();

        if (null == $resultSet)
            return 0;

        return $resultSet;
    }

    public function getDeliveredAmountByAcademicYear(AcademicYear $academicYear)
    {
        return $this->getDeliveredAmountBetween($academicYear->getStartDate(), $academicYear->getEndDate());
    }

    public function getDeliveredAmountBetween(DateTime $startDate, DateTime $endDate)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('SUM(d.number * a.purchasePrice)')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();

        if (null == $resultSet)
            return 0;

        return $resultSet;
    }

    public function getNumberByAcademicYear(AcademicYear $academicYear)
    {
        return $this->getNumberBetween($academicYear->getStartDate(), $academicYear->getEndDate());
    }

    public function getNumberBetween(DateTime $startDate, DateTime $endDate)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('SUM(d.number)')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();

        if (null == $resultSet)
            return 0;

        return $resultSet;
    }

    public function findAllByAcademicYearQuery(AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $query;
    }

    public function findAllByArticleAndAcademicYearQuery($article, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.title'), ':article'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('article', '%'.strtolower($article).'%')
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllBySupplierAndAcademicYearQuery($supplier, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->innerJoin('a.supplier', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':supplier'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('supplier', '%'.strtolower($supplier).'%')
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByArticleEntityQuery(Article $article, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('d.article', ':article'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('article', $article)
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllBySupplierEntityQuery(Supplier $supplier, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('supplier', $supplier)
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByArticleTitleAndSupplierAndAcademicYearQuery($title, Supplier $supplier, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.title'), ':title'),
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('title', '%'.strtolower($title).'%')
            ->setParameter('supplier', $supplier)
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $resultSet;
    }
}
