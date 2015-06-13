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

namespace SyllabusBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Subject
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Subject extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->orderBy('s.name')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  string              $name
     * @return \Doctrine\ORM\Query
     */
    public function findAllByNameQuery($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->where(
                $query->expr()->like($query->expr()->lower('s.name'), ':name')
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->orderBy('s.name')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  string              $code
     * @return \Doctrine\ORM\Query
     */
    public function findAllByCodeQuery($code)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->where(
                $query->expr()->like($query->expr()->lower('s.code'), ':code')
            )
            ->setParameter('code', '%' . strtolower($code) . '%')
            ->orderBy('s.code')
            ->getQuery();

        return $resultSet;
    }
}
