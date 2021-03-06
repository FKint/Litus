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

namespace SecretaryBundle\Component\Registration;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Organization,
    CommonBundle\Entity\User\Person\Academic,
    CudiBundle\Entity\Sale\Booking,
    Doctrine\ORM\EntityManager;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Articles
{
    /**
     * @param EntityManager $entityManager
     * @param Academic      $academic
     * @param Organization  $organization
     * @param AcademicYear  $academicYear
     * @param array         $options
     */
    public static function book(EntityManager $entityManager, Academic $academic, Organization $organization, AcademicYear $academicYear, $options = array())
    {
        $ids = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        if (isset($ids[$organization->getId()])) {
            $membershipArticle = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($ids[$organization->getId()]);

            if (null !== $membershipArticle) {
                $booking = $entityManager
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                        $membershipArticle,
                        $academic,
                        $academicYear
                    );

                if (null === $booking) {
                    $booking = new Booking(
                        $entityManager,
                        $academic,
                        $membershipArticle,
                        'assigned',
                        1,
                        true
                    );

                    $entityManager->persist($booking);
                }

                if (isset($options['payed']) && $options['payed']) {
                    $booking->setStatus('sold', $entityManager);
                }
            }
        }

        $tshirts = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.tshirt_article')
        );

        $hasShirt = false;
        foreach ($tshirts as $tshirt) {
            $booking = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                    $entityManager
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($tshirt),
                    $academic,
                    $academicYear
                );

            if (null !== $booking) {
                $hasShirt = true;
                break;
            }
        }

        $enableAssignment = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_automatic_assignment');
        $currentPeriod = $entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
        $currentPeriod->setEntityManager($entityManager);

        if (!empty($tshirts) && !$hasShirt && isset($options['tshirtSize'])) {
            $booking = new Booking(
                $entityManager,
                $academic,
                $entityManager
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($tshirts[$options['tshirtSize']]),
                'booked',
                1,
                true
            );

            $entityManager->persist($booking);

            if ($enableAssignment) {
                $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
                if ($available > 0) {
                    if ($available >= $booking->getNumber()) {
                        $booking->setStatus('assigned', $entityManager);
                    }
                }
            }
        }

        $dissableAssignment = $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.dissable_registration_articles_2nd_stock_period');

        if (!($dissableAssignment == '1' && ($currentPeriod->getStartDate()->format('n') < 4 || $currentPeriod->getStartDate()->format('n') > 11))) {
            $registrationArticles = unserialize(
                $entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.registration_articles')
            );

            foreach ($registrationArticles as $registrationArticle) {
                $booking = $entityManager
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                        $entityManager
                            ->getRepository('CudiBundle\Entity\Sale\Article')
                            ->findOneById($registrationArticle),
                        $academic,
                        $academicYear
                    );

                if (null !== $booking) {
                    continue;
                }

                $booking = new Booking(
                    $entityManager,
                    $academic,
                    $entityManager
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($registrationArticle),
                    'booked',
                    1,
                    true
                );
                $entityManager->persist($booking);

                if ($enableAssignment) {
                    $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
                    if ($available > 0) {
                        if ($available >= $booking->getNumber()) {
                            $booking->setStatus('assigned', $entityManager);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param \Doctrine\ORM\EntityManager               $entityManager
     * @param \CommonBundle\Entity\User\Person\Academic $academic
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     */
    public static function cancel(EntityManager $entityManager, Academic $academic, AcademicYear $academicYear)
    {
        $ids = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        foreach ($ids as $id) {
            $membershipArticle = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($id);

            $booking = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneBookedOrAssignedByArticleAndPersonInAcademicYear(
                    $membershipArticle,
                    $academic,
                    $academicYear
                );

            if (null !== $booking) {
                $booking->setStatus('canceled', $entityManager);
            }
        }

        $tshirts = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.tshirt_article')
        );

        foreach ($tshirts as $tshirt) {
            $booking = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneBookedOrAssignedByArticleAndPersonInAcademicYear(
                    $entityManager
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($tshirt),
                    $academic,
                    $academicYear
                );

            if (null !== $booking) {
                $booking->setStatus('canceled', $entityManager);
            }
        }

        $registrationArticles = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.registration_articles')
        );

        foreach ($registrationArticles as $registrationArticle) {
            $booking = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneBookedOrAssignedByArticleAndPersonInAcademicYear(
                    $entityManager
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($registrationArticle),
                    $academic,
                    $academicYear
                );

            if (null !== $booking) {
                $booking->setStatus('canceled', $entityManager);
            }
        }
    }
}
