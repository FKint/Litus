<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Component\WebSocket\Sale;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\WebSocket\User,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CudiBundle\Entity\Sales\Booking,
    CudiBundle\Entity\Sales\SaleItem,
    Doctrine\ORM\EntityManager;

/**
 * QueueItem Object
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class QueueItem extends \CommonBundle\Component\WebSocket\Server
{
    /**
     * @var \CudiBundle\Entity\Sales\Session The sale session
     */
    private $_id;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \CommonBundle\Component\WebSocket\User
     */
    private $_user;

    /**
     * @var array
     */
    private $_articles;

    /**
     * @param Doctrine\ORM\EntityManager $entityManager
     * @param integer $id The id of the queue item
     */
    public function __construct(EntityManager $entityManager, User $user, $id)
    {
        $this->_entityManager = $entityManager;
        $this->_id = $id;
        $this->_user = $user;
        $this->_articles = array();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param \CommonBundle\Component\WebSocket\User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
        return $this;
    }

    /**
     * @return \CommonBundle\Component\WebSocket\User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @return boolean
     */
    public function isLocked()
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->_id);

        return ($item->getStatus() == 'collecting' || $item->getStatus() == 'selling');
    }

    /**
     * @return \CudiBundle\Entity\Sales\QueueItem
     */
    public function getItem()
    {
        return $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->_id);
    }

    /**
     * @return string
     */
    public function getCollectInfo()
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->_id);

        return json_encode(
            array(
                'collect' => array(
                    'id' => $item->getId(),
                    'comment' => $item->getComment(),
                    'person' => array(
                        'id' => $item->getPerson()->getId(),
                        'name' => $item->getPerson()->getFullName(),
                        'universityIdentification' => $item->getPerson()->getUniversityIdentification(),
                        'member' => $item->getPerson()->isMember($this->_getCurrentAcademicYear()),
                    ),
                    'articles' => $this->_getArticles(),
                )
            )
        );
    }

    /**
     * @return string
     */
    public function getSaleInfo()
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->_id);

        return json_encode(
            array(
                'sale' => array(
                    'id' => $item->getId(),
                    'comment' => $item->getComment(),
                    'person' => array(
                        'id' => $item->getPerson()->getId(),
                        'name' => $item->getPerson()->getFullName(),
                        'universityIdentification' => $item->getPerson()->getUniversityIdentification(),
                        'member' => false,// $item->getPerson()->isMember($this->_getCurrentAcademicYear()),
                    ),
                    'articles' => $this->_getArticles(),
                )
            )
        );
    }

    /**
     * @param array
     */
    public function setCollectedArticles($articles)
    {
        $this->_articles = $articles;
    }

    /**
     * @param array $articles
     * @param array $discounts
     * @return array
     */
    public function conclude($articles, $discounts)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->_id);

        $bookings = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllOpenByPerson($item->getPerson());

        $soldArticles = array();

        foreach($bookings as $booking) {
            if (!isset($articles->{$booking->getArticle()->getId()}) || $articles->{$booking->getArticle()->getId()} == 0)
                continue;

            if ($articles->{$booking->getArticle()->getId()} < $booking->getNumber()) {
                $remainder = new Booking(
                    $this->_entityManager,
                    $booking->getPerson(),
                    $booking->getArticle(),
                    'assigned',
                    $booking->getNumber() - $articles->{$booking->getArticle()->getId()}
                );
                $this->_entityManager->persist($remainder);
                $booking->setNumber($articles->{$booking->getArticle()->getId()})
                    ->setStatus('sold', $this->_entityManager);
            } else {
                $articles->{$booking->getArticle()->getId()} -= $booking->getNumber();
                $booking->setStatus('sold', $this->_entityManager);
            }

            if (isset($soldArticles[$booking->getArticle()->getId()])) {
                $soldArticles[$booking->getArticle()->getId()]['number'] += $booking->getNumber();
            } else {
                $soldArticles[$booking->getArticle()->getId()] = array(
                    'article' => $booking->getArticle(),
                    'number' => $booking->getNumber(),
                );
            }
        }

        foreach($articles as $id => $number) {
            if ($number <= 0)
                continue;

            $article = $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sales\Article')
                ->findOneById($id);

            $booking = new Booking(
                $this->_entityManager,
                $item->getPerson(),
                $article,
                'sold',
                $number
            );
            $this->_entityManager->persist($booking);

            if (isset($soldArticles[$booking->getArticle()->getId()])) {
                $soldArticles[$booking->getArticle()->getId()]['number'] += $booking->getNumber();
            } else {
                $soldArticles[$booking->getArticle()->getId()] = array(
                    'article' => $booking->getArticle(),
                    'number' => $booking->getNumber(),
                );
            }
        }

        $saleItems = array();
        foreach($soldArticles as $soldArticle) {
            $price = $soldArticle['article']->getSellPrice();
            foreach($soldArticle['article']->getDiscounts() as $discount) {
                if (in_array($discount->getType(), $discounts)) {
                    if ($discount->getType() == 'member' && !$item->getPerson()->isMember($this->_getCurrentAcademicYear()))
                        continue;
                    $price = $discount->apply($soldArticle['article']->getSellPrice());
                }
            }

            $saleItem = new SaleItem(
                $soldArticle['article'],
                $soldArticle['number'],
                $price * $soldArticle['number'] / 100,
                $item
            );
            $this->_entityManager->persist($saleItem);
            $saleItems[] = $saleItem;

            $soldArticle['article']->setStockValue($soldArticle['article']->getStockValue() - $soldArticle['number']);
        }

        $this->_entityManager->flush();

        return $saleItems;
    }

    private function _getArticles()
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->_id);

        $bookings = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllOpenByPerson($item->getPerson());

        $results = array();
        $bookedArticles = array();
        foreach($bookings as $booking) {
            $barcodes = array();
            foreach($booking->getArticle()->getBarcodes() as $barcode)
                $barcodes[] = $barcode->getBarcode();

            $bookedArticles[] = $booking->getArticle()->getId();

            if (isset($results[$booking->getStatus() . '_' . $booking->getArticle()->getId()])) {
                $results[$booking->getStatus() . '_' . $booking->getArticle()->getId()]['number'] += $booking->getNumber();
            } else {
                $result = array(
                    'id' => $booking->getId(),
                    'articleId' => $booking->getArticle()->getId(),
                    'price' => (int) $booking->getArticle()->getSellPrice(),
                    'title' => $booking->getArticle()->getMainArticle()->getTitle(),
                    'barcode' => $booking->getArticle()->getBarcode(),
                    'barcodes' => $barcodes,
                    'author' => $booking->getArticle()->getMainArticle()->getAuthors(),
                    'number' => $booking->getNumber(),
                    'status' => $booking->getStatus(),
                    'collected' => isset($this->_articles->{$booking->getArticle()->getId()}) ? $this->_articles->{$booking->getArticle()->getId()} : 0,
                    'discounts' => array(),
                );

                foreach($booking->getArticle()->getDiscounts() as $discount)
                    $result['discounts'][] = array('type' => $discount->getRawType(), 'value' => $discount->apply($booking->getArticle()->getSellPrice()));

                $results[$booking->getStatus() . '_' . $booking->getArticle()->getId()] = $result;
            }
        }

        foreach($this->_articles as $id => $number) {
            if (!in_array($id, $bookedArticles) && $number > 0) {
                $article = $this->_entityManager
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findOneById($id);

                $barcodes = array();
                foreach($article->getBarcodes() as $barcode)
                    $barcodes[] = $barcode->getBarcode();

                $result = array(
                    'id' => $booking->getId(),
                    'articleId' => $article->getId(),
                    'price' => $article->getSellPrice(),
                    'title' => $article->getMainArticle()->getTitle(),
                    'barcode' => $article->getBarcode(),
                    'barcodes' => $barcodes,
                    'author' => $article->getMainArticle()->getAuthors(),
                    'number' => 1,
                    'status' => 'assigned',
                    'collected' => $number,
                    'discounts' => array(),
                );

                foreach($article->getDiscounts() as $discount)
                    $result['discounts'][] = array('type' => $discount->getRawType(), 'value' => $discount->apply($article->getSellPrice()));
                $results['assigned_' . $article->getId()] = $result;
            }
        }

        return array_values($results);
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    private function _getCurrentAcademicYear()
    {
        $startAcademicYear = AcademicYear::getStartOfAcademicYear();
        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        if (null === $academicYear) {
            $organizationStart = str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->_entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('start_organization_year')
            );
            $organizationStart = new DateTime($organizationStart);
            $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
            $this->_entityManager->persist($academicYear);
            $this->_entityManager->flush();
        }

        return $academicYear;
    }
}
