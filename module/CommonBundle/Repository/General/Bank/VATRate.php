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

namespace CommonBundle\Repository\General\Bank;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class VATRate extends EntityRepository
{
    public function findByCode($code)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('u')
        ->from('CommonBundle\Entity\General\Bank\VATRate', 'vr')
        ->where(
                $query->expr()->eq('vr.code', ':code')
        )
        ->setParameter('code', $code)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
        
        return $resultSet;
    }
}
