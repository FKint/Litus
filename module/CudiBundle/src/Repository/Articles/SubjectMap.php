<?php

namespace CudiBundle\Repository\Articles;

use CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Entity\Article,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr\Join,
    SyllabusBundle\Entity\Subject as SubjectEntity;

/**
 * SubjectMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubjectMap extends EntityRepository
{
    public function findOneByArticleAndSubjectAndAcademicYear(Article $article, SubjectEntity $subject, AcademicYear $academicYear, $isProf = false)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
        	->from('CudiBundle\Entity\Articles\SubjectMap', 'm')
        	->where(
        	    $query->expr()->andX(
        	        $query->expr()->eq('m.removed', 'false'),
        	        $query->expr()->eq('m.article', ':article'),
        	        $query->expr()->eq('m.subject', ':subject'),
        	        $query->expr()->eq('m.academicYear', ':academicYear'),
        	        $isProf ? '1=1' : $query->expr()->eq('m.isProf', 'false')
        	    )
        	)
        	->setParameter('article', $article->getId())
        	->setParameter('subject', $subject->getId())
        	->setParameter('academicYear', $academicYear->getId())
        	->setMaxResults(1)
        	->getQuery()
        	->getResult();
        	
        if (isset($resultSet[0]))
        	return $resultSet[0];
        
        return null;
    }
    
    public function findAllBySubjectAndAcademicYear(SubjectEntity $subject, AcademicYear $academicYear, $isProf = false)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
        	->from('CudiBundle\Entity\Articles\SubjectMap', 'm')
        	->innerJoin('m.article', 'a', Join::WITH,
        	    $query->expr()->eq('a.isHistory', 'false')
        	)
        	->where(
        	    $query->expr()->andX(
        	        $query->expr()->eq('m.removed', 'false'),
        	        $query->expr()->eq('m.subject', ':subject'),
        	        $query->expr()->eq('m.academicYear', ':academicYear'),
        	        $isProf ? '1=1' : $query->expr()->eq('m.isProf', 'false')
        	    )
        	)
        	->setParameter('subject', $subject->getId())
        	->setParameter('academicYear', $academicYear->getId())
        	->getQuery()
        	->getResult();
        	
        return $resultSet;
    }
    
    public function findAllByArticleAndAcademicYear(Article $article, AcademicYear $academicYear, $isProf = false)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
        	->from('CudiBundle\Entity\Articles\SubjectMap', 'm')
        	->where(
        	    $query->expr()->andX(
        	        $query->expr()->eq('m.removed', 'false'),
        	        $query->expr()->eq('m.article', ':article'),
        	        $query->expr()->eq('m.academicYear', ':academicYear'),
        	        $isProf ? '1=1' : $query->expr()->eq('m.isProf', 'false')
        	    )
        	)
        	->setParameter('article', $article->getId())
        	->setParameter('academicYear', $academicYear->getId())
        	->getQuery()
        	->getResult();
        	
        return $resultSet;
    }
}