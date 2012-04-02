<?php

namespace CudiBundle\Repository;

use CudiBundle\Entity\Article as ArticleEntity,
    Doctrine\ORM\EntityRepository,
    SyllabusBundle\Entity\Subject;

/**
 * ArticleSubjectMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleSubjectMap extends EntityRepository
{
    public function findAllByArticle(ArticleEntity $article)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
        	->from('CudiBundle\Entity\ArticleSubjectMap', 'm')
        	->where($query->expr()->eq('m.article', ':article'))
        	->setParameter('article', $article->getId())
        	->getQuery()
        	->getResult();
        	
        return $resultSet;
    }
    
    public function findAllBySubject(Subject $subject)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
        	->from('CudiBundle\Entity\ArticleSubjectMap', 'm')
        	->where($query->expr()->eq('m.subject', ':subject'))
        	->setParameter('subject', $subject->getId())
        	->getQuery()
        	->getResult();
        	
        return $resultSet;
    }
    
    public function findOneByArticleAndSubject(ArticleEntity $article, Subject $subject)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
        	->from('CudiBundle\Entity\ArticleSubjectMap', 'm')
        	->where(
        	    $query->expr()->andX(
        	        $query->expr()->eq('m.subject', ':subject'),
        	        $query->expr()->eq('m.article', ':article')
        	    )
        	)
        	->setParameter('subject', $subject->getId())
        	->setParameter('article', $article->getId())
        	->setMaxResults(1)
        	->getQuery()
        	->getResult();
        
        if (isset($resultSet[0]))
        	return $resultSet[0];
        
        return null;
    }
}