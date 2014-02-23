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

namespace BrBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Contract
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Contract extends EntityRepository
{
    public function findAllContractIds()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('c.id')
            ->from('BrBundle\Entity\Contract', 'c')
            ->getQuery();

        $return = array();
        foreach ($resultSet as $result)
            $return[] = $result['id'];

        return $return;
    }

    public function findNextInvoiceNb()
    {
        $query = $this->_em->createQueryBuilder();
        $highestInvoiceNb = $query->select('MAX(c.invoiceNb)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->getQuery()
            ->getSingleScalarResult();

        return ++$highestInvoiceNb;
    }

    public function findNextContractNb()
    {
        $query = $this->_em->createQueryBuilder();
        $highestInvoiceNb = $query->select('MAX(c.contractNb)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->getQuery()
            ->getSingleScalarResult();

        return ++$highestContractNb;
    }
}
