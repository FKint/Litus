<?php

namespace CudiBundle\Repository\Sales;

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Organisation,
    CudiBundle\Entity\Article as ArticleEntity,
    CudiBundle\Entity\Supplier,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr\Join;

/**
 * Article
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Article extends EntityRepository
{
    public function findAllByAcademicYear(AcademicYear $academicYear, $semester = 0, Organisation $organisation = null)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            );

        if (null !== $organisation) {
            $query->andWhere($query->expr()->eq('a.organisation', ':organisation'))
                ->setParameter('organisation', $organisation);
        }

        $resultSet = $query->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByAcademicYearSortBarcode(AcademicYear $academicYear)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->getQuery()
            ->getResult();

        $barcodes = array();
        foreach($resultSet as $article)
            $barcodes[] = $article->getBarcode();

        array_multisort($barcodes, $resultSet);

        return $resultSet;
    }

    public function findOneByArticle(ArticleEntity $article)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.mainArticle', ':article')
                )
            )
            ->setParameter('article', $article->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

       if (isset($resultSet[0]))
           return $resultSet[0];

       return null;
    }

    public function findOneById($id)
    {

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
        ->from('CudiBundle\Entity\Sales\Article', 'a')
        ->where(
            $query->expr()->eq('a.id', ':id')
        )
        ->setParameter('id', $id)
        ->setMaxResults(1)
        ->getQuery()
        ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findOneByBarcode($barcode)
    {
        $barcode = $this->_em
            ->getRepository('CudiBundle\Entity\Sales\Articles\Barcode')
            ->findOneByBarcode($barcode);

        if (isset($barcode))
            return $barcode->getArticle();

        return null;
    }

    public function findAllByTypeAndAcademicYear($type, AcademicYear $academicYear)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
        ->from('CudiBundle\Entity\Sales\Article', 'a')
        ->innerJoin('a.mainArticle', 'm')
        ->where(
            $query->expr()->andX(
                $query->expr()->eq('m.type', ':type'),
                $query->expr()->eq('a.isHistory', 'false'),
                $query->expr()->eq('m.isHistory', 'false'),
                $query->expr()->eq('m.isProf', 'false'),
                $query->expr()->in('m.id', $articles)
            )
        )
        ->setParameter('type', $type)
        ->orderBy('m.title', 'ASC')
        ->getQuery()
        ->getResult();

        return $resultSet;
    }

    public function findAllByTitleAndAcademicYear($title, AcademicYear $academicYear, $semester = 0, Organisation $organisation = null)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.title'), ':title'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('title', '%'.strtolower($title).'%');

        if (null !== $organisation) {
            $query->andWhere($query->expr()->eq('a.organisation', ':organisation'))
                ->setParameter('organisation', $organisation);
        }

        $resultSet = $query->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByAuthorAndAcademicYear($author, AcademicYear $academicYear, $semester = 0, Organisation $organisation = null)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.authors'), ':author'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('author', '%'.strtolower($author).'%');

        if (null !== $organisation) {
            $query->andWhere($query->expr()->eq('a.organisation', ':organisation'))
                ->setParameter('organisation', $organisation);
        }

        $resultSet = $query->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByTitleOrAuthorAndAcademicYear($string, AcademicYear $academicYear)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, 0);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->like($query->expr()->lower('m.title'), ':string'),
                        $query->expr()->like($query->expr()->lower('m.authors'), ':string')
                    ),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('string', '%'.strtolower($string).'%')
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByPublisherAndAcademicYear($publisher, AcademicYear $academicYear, $semester = 0, Organisation $organisation = null)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.publishers'), ':publisher'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('publisher', '%'.strtolower($publisher).'%');

        if (null !== $organisation) {
            $query->andWhere($query->expr()->eq('a.organisation', ':organisation'))
                ->setParameter('organisation', $organisation);
        }

        $resultSet = $query->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByBarcodeAndAcademicYear($barcode, AcademicYear $academicYear, $semester = 0, Organisation $organisation = null)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $query->select('b')
            ->from('CudiBundle\Entity\Sales\Articles\Barcode', 'b')
            ->innerJoin('b.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->concat('b.barcode', '\'\''), ':barcode'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('barcode', '%'.$barcode.'%');

        if (null !== $organisation) {
            $query->andWhere($query->expr()->eq('a.organisation', ':organisation'))
                ->setParameter('organisation', $organisation);
        }

        $resultSet = $query->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        $articles = array();
        foreach($resultSet as $barcode)
            $articles[] = $barcode->getArticle();

        return $articles;
    }

    public function findAllBySupplierStringAndAcademicYear($supplier, AcademicYear $academicYear, $semester = 0, Organisation $organisation = null)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->innerJoin('a.supplier', 's', Join::WITH,
                $query->expr()->like($query->expr()->lower('s.name'), ':supplier')
            )
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('supplier', '%' . strtolower($supplier) . '%');

        if (null !== $organisation) {
            $query->andWhere($query->expr()->eq('a.organisation', ':organisation'))
                ->setParameter('organisation', $organisation);
        }

        $resultSet = $query->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllBySupplier(Supplier $supplier)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false')
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByTitleAndAcademicYearTypeAhead($title, AcademicYear $academicYear)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Articles\Barcode', 'b')
            ->innerJoin('b.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false'),
                    $query->expr()->orX(
                        $query->expr()->like($query->expr()->lower('m.title'), ':title'),
                        $query->expr()->like($query->expr()->concat('b.barcode', '\'\''), ':barcode')
                    ),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('title', '%'.strtolower($title).'%')
            ->setParameter('barcode', strtolower($title).'%')
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        $articles = array();
        foreach($resultSet as $barcode)
            $articles[$barcode->getArticle()->getId()] = $barcode->getArticle();

        return $articles;
    }

    private function _getArticleIdsBySemester(AcademicYear $academicYear, $semester = 0)
    {
        $query = $this->_em->createQueryBuilder();
        if ($semester == 0) {
            $resultSet = $query->select('a.id')
                ->from('CudiBundle\Entity\Articles\SubjectMap', 'm')
                ->innerJoin('m.article', 'a')
                ->innerJoin('m.subject', 's')
                ->where(
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
                ->setParameter('academicYear', $academicYear->getId())
                ->getQuery()
                ->getResult();
        } else {
            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('a.id')
                ->from('CudiBundle\Entity\Articles\SubjectMap', 'm')
                ->innerJoin('m.article', 'a')
                ->innerJoin('m.subject', 's')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->orX(
                            $query->expr()->eq('s.semester', '0'),
                            $query->expr()->eq('s.semester', ':semester')
                        ),
                        $query->expr()->eq('m.academicYear', ':academicYear')
                    )
                )
                ->setParameter('semester', $semester)
                ->setParameter('academicYear', $academicYear->getId())
                ->getQuery()
                ->getResult();
        }

        $articles = array(0);
        foreach ($resultSet as $item)
            $articles[$item['id']] = $item['id'];

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a.id')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->eq('a.type', '\'common\'')
            )
            ->getQuery()
            ->getResult();

        foreach ($resultSet as $item)
            $articles[$item['id']] = $item['id'];

        return $articles;
    }
}
