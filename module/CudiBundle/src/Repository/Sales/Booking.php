<?php

namespace CudiBundle\Repository\Sales;

use CommonBundle\Entity\Users\Person,
    Exception,
    CudiBundle\Component\Mail\Booking as BookingMail,
    CudiBundle\Entity\Log\Sales\Assignments as LogAssignments,
    CudiBundle\Entity\Sales\Article as ArticleEntity,
    CudiBundle\Entity\Sales\Booking as BookingEntity,
    CudiBundle\Entity\Stock\Period,
    DateTime,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr\Join,
    Zend\Mail\Transport\TransportInterface;

/**
 * Booking
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Booking extends EntityRepository
{
    public function findAllActiveByPeriod(Period $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->eq('b.status', '\'booked\''),
                        $query->expr()->eq('b.status', '\'assigned\'')
                    ),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter('startDate', $period->getStartDate());

            if (!$period->isOpen())
                $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->orderBy('b.bookDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByPersonAndPeriod(Person $person, Period $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.person', ':person'),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter('person', $person->getId())
            ->setParameter('startDate', $period->getStartDate());

            if (!$period->isOpen())
                $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->orderBy('b.bookDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByArticleAndPeriod(ArticleEntity $article, Period $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.article', ':article'),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter('article', $article->getId())
            ->setParameter('startDate', $period->getStartDate());

            if (!$period->isOpen())
                $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->orderBy('b.bookDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllInactiveByPeriod(Period $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->not(
                        $query->expr()->orX(
                            $query->expr()->eq('b.status', '\'booked\''),
                            $query->expr()->eq('b.status', '\'assigned\'')
                        )
                    ),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter('startDate', $period->getStartDate());

            if (!$period->isOpen())
                $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->orderBy('b.bookDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByPersonNameAndTypeAndPeriod($person, $type, Period $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->innerJoin('b.person', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->andX(
                            $query->expr()->eq('\'active\'', ':type'),
                            $query->expr()->orX(
                                $query->expr()->eq('b.status', '\'booked\''),
                                $query->expr()->eq('b.status', '\'assigned\'')
                            )
                        ),
                        $query->expr()->andX(
                            $query->expr()->eq('\'inactive\'', ':type'),
                            $query->expr()->not(
                                $query->expr()->orX(
                                    $query->expr()->eq('b.status', '\'booked\''),
                                    $query->expr()->eq('b.status', '\'assigned\'')
                                )
                            )
                        )
                    ),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate'),
                    $query->expr()->orX(
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('p.firstName', "' '")),
                                $query->expr()->lower('p.lastName')
                            ),
                            ':name'
                        ),
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('p.lastName', "' '")),
                                $query->expr()->lower('p.firstName')
                            ),
                            ':name'
                        )
                    )
                )
            )
            ->setParameter('name', '%'.strtolower($person).'%')
            ->setParameter('type', $type)
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->orderBy('b.bookDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByArticleAndTypeAndPeriod($article, $type, Period $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->innerJoin('b.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->andX(
                            $query->expr()->eq('\'active\'', ':type'),
                            $query->expr()->orX(
                                $query->expr()->eq('b.status', '\'booked\''),
                                $query->expr()->eq('b.status', '\'assigned\'')
                            )
                        ),
                        $query->expr()->andX(
                            $query->expr()->eq('\'inactive\'', ':type'),
                            $query->expr()->not(
                                $query->expr()->orX(
                                    $query->expr()->eq('b.status', '\'booked\''),
                                    $query->expr()->eq('b.status', '\'assigned\'')
                                )
                            )
                        )
                    ),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate'),
                    $query->expr()->like($query->expr()->lower('m.title'), ':article')
                )
            )
            ->setParameter('article', '%'.strtolower($article).'%')
            ->setParameter('type', $type)
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->orderBy('b.bookDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByStatusAndTypeAndPeriod($status, $type, Period $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->andX(
                            $query->expr()->eq('\'active\'', ':type'),
                            $query->expr()->orX(
                                $query->expr()->eq('b.status', '\'booked\''),
                                $query->expr()->eq('b.status', '\'assigned\'')
                            )
                        ),
                        $query->expr()->andX(
                            $query->expr()->eq('\'inactive\'', ':type'),
                            $query->expr()->not(
                                $query->expr()->orX(
                                    $query->expr()->eq('b.status', '\'booked\''),
                                    $query->expr()->eq('b.status', '\'assigned\'')
                                )
                            )
                        )
                    ),
                    $query->expr()->like($query->expr()->lower('b.status'), ':status'),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter('status', '%'.strtolower($status).'%')
            ->setParameter('type', $type)
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->orderBy('b.bookDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllBooked(Period $period = null)
    {
        if (null == $period) {
            $period = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findOneActive();
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.status', '\'booked\''),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter('startDate', $period->getStartDate());

            if (!$period->isOpen())
                $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->orderBy('b.bookDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllBookedArticles()
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.status', '\'booked\''),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter('startDate', $period->getStartDate());

            if (!$period->isOpen())
                $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->orderBy('b.bookDate', 'DESC')
            ->getQuery()
            ->getResult();

        $articles = array();
        foreach($resultSet as $booking)
            $articles[$booking->getArticle()->getId()] = $booking->getArticle();

        return $articles;
    }

    public function findAllBookedByArticleAndPeriod(ArticleEntity $article, Period $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.article', ':article'),
                    $query->expr()->eq('b.status', '\'booked\''),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter('article', $article->getId())
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->orderBy('b.bookDate', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllAssignedByPerson(Person $person)
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.person', ':person'),
                    $query->expr()->eq('b.status', '\'assigned\''),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter(':person', $person->getId())
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findOneAssignedByArticleAndPerson(ArticleEntity $article, Person $person)
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.person', ':person'),
                    $query->expr()->eq('b.article', ':article'),
                    $query->expr()->eq('b.status', '\'assigned\''),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter(':person', $person->getId())
            ->setParameter(':article', $article->getId())
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findOneSoldByArticleAndPerson(ArticleEntity $article, Person $person)
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.person', ':person'),
                    $query->expr()->eq('b.article', ':article'),
                    $query->expr()->eq('b.status', '\'sold\''),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter(':person', $person->getId())
            ->setParameter(':article', $article->getId())
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findOneSoldOrAssignedOrBookedByArticleAndPerson(ArticleEntity $article, Person $person)
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.person', ':person'),
                    $query->expr()->eq('b.article', ':article'),
                    $query->expr()->orX(
                        $query->expr()->eq('b.status', '\'sold\''),
                        $query->expr()->eq('b.status', '\'assigned\''),
                        $query->expr()->eq('b.status', '\'booked\'')
                    ),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter(':person', $person->getId())
            ->setParameter(':article', $article->getId())
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findAllOpenByPerson(Person $person)
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        if ($period === null)
            throw new Exception("There is no active stock period!");

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where($query->expr()->andX(
                    $query->expr()->eq('b.person', ':person'),
                    $query->expr()->neq('b.status', '\'sold\''),
                    $query->expr()->neq('b.status', '\'expired\''),
                    $query->expr()->neq('b.status', '\'canceled\''),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter(':person', $person->getId())
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllSoldByPerson(Person $person)
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        if ($period === null)
            throw new Exception("There is no active stock period!");

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where($query->expr()->andX(
                    $query->expr()->eq('b.person', ':person'),
                    $query->expr()->eq('b.status', '\'sold\''),
                    $query->expr()->gte('b.bookDate', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
                )
            )
            ->setParameter(':person', $person->getId())
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findOneById($id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('b')
        ->from('CudiBundle\Entity\Sales\Booking', 'b')
        ->where(
            $query->expr()->eq('b.id', ':id')
        )
        ->setParameter('id', $id)
        ->setMaxResults(1)
        ->getQuery()
        ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findOneSoldByPersonAndArticle(Person $person, ArticleEntity $article)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.person', ':person'),
                    $query->expr()->eq('b.article', ':article'),
                    $query->expr()->eq('b.status', '\'sold\'')
                )
            )
            ->setParameter('person', $person->getId())
            ->setParameter('article', $article->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findOneSoldByArticleAndNumber(ArticleEntity $article, $number)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.article', ':article'),
                    $query->expr()->eq('b.number', ':number'),
                    $query->expr()->eq('b.status', ':status')
                )
            )
            ->setParameter('number', $number)
            ->setParameter('status', 'sold')
            ->setParameter('article', $article->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];
    }

    public function assignAll(Person $person, TransportInterface $mailTransport)
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        $period->setEntityManager($this->getEntityManager());

        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllBookedArticles();

        $counter = 0;
        $idsAssigned = array();

        $persons = array();

        foreach($articles as $article) {
            $available = $article->getStockValue() - $period->getNbAssigned($article);

            if ($available <= 0)
                continue;

            $bookings = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Booking')
                ->findAllBookedByArticleAndPeriod($article, $period);

            foreach($bookings as $booking) {
                if ($available <= 0)
                    break;

                $counter++;

                if ($available < $booking->getNumber()) {
                    $new = new BookingEntity(
                        $this->getEntityManager(),
                        $booking->getPerson(),
                        $booking->getArticle(),
                        'booked',
                        $booking->getNumber() - $available
                    );
                    $this->getEntityManager()->persist($new);

                    $booking->setNumber($available);
                }

                $booking->setStatus('assigned', $this->getEntityManager());
                $idsAssigned[] = $booking->getId();
                $available -= $booking->getNumber();

                if (!isset($persons[$booking->getPerson()->getId()]))
                    $persons[$booking->getPerson()->getId()] = array('person' => $booking->getPerson(), 'bookings' => array());

                $persons[$booking->getPerson()->getId()]['bookings'][] = $booking;
            }
        }

        if ($counter > 0)
            $this->getEntityManager()->persist(new LogAssignments($person, $idsAssigned));

        $this->getEntityManager()->flush();

        foreach($persons as $person)
            BookingMail::sendMail($this->getEntityManager(), $mailTransport, $person['bookings'], $person['person']);

        return $counter;
    }

    public function expireBookings()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $bookings = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.status', '\'assigned\''),
                    $query->expr()->lt('b.expirationDate', ':now')
                )
            )
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();

        foreach($bookings as $booking) {
               $booking->setStatus('expired', $this->getEntityManager());
        }
        return sizeof($bookings);
    }

    public function findLastAssignedByArticle(ArticleEntity $article)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Booking', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.article', ':article'),
                    $query->expr()->eq('b.status', ':status')
                )
            )
            ->setParameter('status', 'assigned')
            ->setParameter('article', $article->getId())
            ->orderBy('b.assignmentDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
