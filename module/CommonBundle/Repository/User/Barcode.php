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

namespace CommonBundle\Repository\User;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Organization,
    RuntimeException;

/**
 * Barcode
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Barcode extends EntityRepository
{
    public function findOneByBarcode($barcode)
    {
        $ean12Result = null;
        if (is_numeric($barcode)) {
            $eanBarcode = $barcode;
            if (strlen($barcode) == 13) {
                $eanBarcode = floor($barcode / 10);
            }

            $query = $this->_em->createQueryBuilder();
            $ean12Result = $query->select('b')
                ->from('CommonBundle\Entity\User\Barcode\Ean12', 'b')
                ->where(
                    $query->expr()->eq('b.barcode', ':barcode')
                )
                ->setParameter('barcode', $eanBarcode)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }

        $query = $this->_em->createQueryBuilder();
        $qrResult = $query->select('b')
            ->from('CommonBundle\Entity\User\Barcode\Qr', 'b')
            ->where(
                $query->expr()->eq('b.barcode', ':barcode')
            )
            ->setParameter('barcode', $barcode)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (null !== $ean12Result && null !== $qrResult) {
            throw new RuntimeException('Found both an EAN-12 and QR code for this barcode');
        }

        return null !== $ean12Result ? $ean12Result : $qrResult;
    }

    public function findAllByBarcode($barcode)
    {
        $ean12Result = array();
        if (is_numeric($barcode)) {
            $eanBarcode = $barcode;
            if (strlen($barcode) == 13) {
                $eanBarcode = floor($barcode / 10);
            }

            $query = $this->_em->createQueryBuilder();
            $ean12Result = $query->select('b')
                ->from('CommonBundle\Entity\User\Barcode\Ean12', 'b')
                ->where(
                    $query->expr()->like($query->expr()->concat('b.barcode', '\'\''), ':barcode')
                )
                ->setParameter('barcode', strtolower($eanBarcode) . '%')
                ->getQuery()
                ->getResult();
        }

        $query = $this->_em->createQueryBuilder();
        $qrResult = $query->select('b')
            ->from('CommonBundle\Entity\User\Barcode\Qr', 'b')
            ->where(
                $query->expr()->like($query->expr()->concat('b.barcode', '\'\''), ':barcode')
            )
            ->setParameter('barcode', strtolower($barcode) . '%')
            ->getQuery()
            ->getResult();

        return array_merge($qrResult, $ean12Result);
    }

    public function findAllByBarcodeAndOrganization($barcode, AcademicYear $academicYear, Organization $organization)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a.id')
            ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
            ->innerJoin('m.academic', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.organization', ':organization'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('organization', $organization)
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        $ids = array(0);

        foreach ($resultSet as $result) {
            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('a.id')
                ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
                ->innerJoin('m.academic', 'a')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('m.organization', ':organization'),
                        $query->expr()->eq('m.academicYear', ':academicYear')
                    )
                )
                ->setParameter('organization', $organization)
                ->setParameter('academicYear', $academicYear)
                ->getQuery()
                ->getResult();

            foreach ($resultSet as $result) {
                $ids[] = $result['id'];
            }
        }

        $ean12Result = array();
        if (is_numeric($barcode)) {
            $eanBarcode = $barcode;
            if (strlen($barcode) == 13) {
                $eanBarcode = floor($barcode / 10);
            }

            $query = $this->_em->createQueryBuilder();
            $ean12Result = $query->select('b')
                ->from('CommonBundle\Entity\User\Barcode\Ean12', 'b')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->like($query->expr()->concat('b.barcode', '\'\''), ':barcode'),
                        $query->expr()->in('b.person', $ids)
                    )
                )
                ->setParameter('barcode', strtolower($eanBarcode) . '%')
                ->getQuery()
                ->getResult();
        }

        $query = $this->_em->createQueryBuilder();
        $qrResult = $query->select('b')
            ->from('CommonBundle\Entity\User\Barcode\Qr', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->concat('b.barcode', '\'\''), ':barcode'),
                    $query->expr()->in('b.person', $ids)
                )
            )
            ->setParameter('barcode', strtolower($barcode) . '%')
            ->getQuery()
            ->getResult();

        return array_merge($qrResult, $ean12Result);
    }
}
