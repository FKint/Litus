<?php

namespace PageBundle\Repository\Nodes;

use Doctrine\ORM\EntityRepository,
    PageBundle\Entity\Node\Page as PageEntity;

/**
 * Page
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Page extends EntityRepository
{
    public function findAll()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PageBundle\Entity\Node\Page', 'p')
            ->where(
                $query->expr()->isNull('p.endTime')
            )
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findByCategory($category)
    {
        return $this->_em->getRepository('PageBundle\Entity\Node\Page')
            ->findBy(array('category' => $category, 'endTime' => null));
    }

    public function findByParent($parent)
    {
        return $this->_em->getRepository('PageBundle\Entity\Node\Page')
            ->findBy(array('parent' => $parent, 'endTime' => null));
    }

    public function findOneByNameAndParent($name, $parentName)
    {
        if (null === $parentName)
            return $this->findOneByName($name, null);

        $query = $this->_em->createQueryBuilder();
        $query->select('p')
            ->from('PageBundle\Entity\Node\Page', 'p')
            ->innerJoin('p.parent', 'par')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.name', ':name'),
                    $query->expr()->eq('par.name', ':parentName'),
                    $query->expr()->isNull('p.endTime')
                )
            )
            ->setParameter('name', $name)
            ->setParameter('parentName', $parentName);

        $resultSet = $query->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findOneByName($name, PageEntity $parent = null)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('p')
            ->from('PageBundle\Entity\Node\Page', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.name', ':name'),
                    null === $parent
                        ? $query->expr()->isNull('p.parent')
                        : $query->expr()->eq('p.parent', ':parent'),
                    $query->expr()->isNull('p.endTime')
                )
            )
            ->setParameter('name', $name);

        if (null !== $parent)
            $query->setParameter('parent', $parent);

        $resultSet = $query->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
