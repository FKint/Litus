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

namespace QuizBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    QuizBundle\Entity\Quiz as QuizEntity;

/**
 * Round
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Round extends EntityRepository
{
    /**
     * Gets all rounds belonging to a quiz
     * @param QuizEntity $quiz The quiz the rounds must belong to
     */
    public function findAllByQuizQuery(QuizEntity $quiz)
    {
        $query = $this->_em->createQueryBuilder();

        $resultSet = $query->select('r')
            ->from('QuizBundle\Entity\Round', 'r')
            ->where(
                $query->expr()->eq('r.quiz', ':quiz')
            )
            ->orderBy('r.order', 'ASC')
            ->setParameter('quiz', $quiz)
            ->getQuery();

        return $resultSet;
    }

    /**
     * Gets the order for the next round in the quiz
     * @param  QuizEntity $quiz
     * @return integer
     */
    public function getNextRoundOrderForQuiz(QuizEntity $quiz)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('MAX(r.order)')
            ->from('QuizBundle\Entity\Round', 'r')
            ->where(
                $query->expr()->eq('r.quiz', ':quiz')
            )
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getSingleScalarResult();

        if ($resultSet === null) {
            return 1;
        }

        return $resultSet + 1;
    }
}
