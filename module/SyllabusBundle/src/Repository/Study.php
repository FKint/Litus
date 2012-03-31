<?php

namespace SyllabusBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Study
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Study extends EntityRepository
{
    public function findAllStudies()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
        	->from('SyllabusBundle\Entity\Study', 's')
        	->where(
        	    $query->expr()->andX(
        	        $query->expr()->eq('s.active', 'true'),
       	            $query->expr()->notIn('s.id', $this->_findParentStudiesIds())
        	    )
        	)
        	->getQuery()
        	->getResult();
        	
        return $resultSet;
    }
    
    public function findAllByTitle($title)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
        	->from('SyllabusBundle\Entity\Study', 's')
        	->where(
        	    $query->expr()->andX(
        	        $query->expr()->eq('s.active', 'true'),
                    $query->expr()->notIn('s.id', $this->_findParentStudiesIds())
        	    )
        	)
        	->getQuery()
        	->getResult();
        	
        $result = array();
        
        $title = strtolower($title);
        
        foreach($resultSet as $study) {
            if (strpos(strtolower($study->getFullTitle()), $title) !== false)
                $result[] = $study;
        }
        
        return $result;
    }
    
    private function _findParentStudiesIds()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('DISTINCT IDENTITY(s.parent)')
            ->from('SyllabusBundle\Entity\Study', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->isNotNull('s.parent')
                )
            )
            ->getQuery()
            ->getResult();
            
        $ids = array();
        foreach($resultSet as $id)
            $ids[] = $id[1];

        return $ids;
    }
    
    public function findOneByTitlePhaseAndLanguage($title, $phase, $language)
    {
        if (! is_numeric($phase))
            return null;
        
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
        	->from('SyllabusBundle\Entity\Study', 's')
        	->where(
        	    $query->expr()->andX(
        	        $query->expr()->eq('s.title', ':title'),
        	        $query->expr()->eq('s.phase', ':phase'),
        	        $query->expr()->eq('s.language', ':language')        	        
        	    )
        	)
        	->setParameter('title', $title)
        	->setParameter('phase', $phase)
        	->setParameter('language', $language)
        	->setMaxResults(1)
    		->getQuery()
    		->getResult();
    	
    	if (isset($resultSet[0]))
    		return $resultSet[0];
    	
    	return null;
    }
    
}