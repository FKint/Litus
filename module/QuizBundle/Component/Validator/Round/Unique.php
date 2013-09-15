<?php

namespace QuizBundle\Component\Validator\Round;

use Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Quiz,
    QuizBundle\Entity\Round;

/**
 * Validates the uniqueness of a round number in a quiz
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class Unique extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \QuizBundle\Entity\Quiz The quiz where the round belongs to
     */
    private $_quiz = null;

    /**
     * @var \QuizBundle\Entity\Round The round excluded
     */
    private $_round = null;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The round number already exists'
    );

    /**
     *
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \QuizBundle\Entity\Quiz $quiz The quiz where the round belongs to
     * @param \QuizBundle\Entity\Round $round The round excluded
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, Quiz $quiz, Round $round = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_quiz = $quiz;
        $this->_round = $round;
    }

    public function isValid($value) {
        $this->setValue($value);

        if (!is_numeric($value)) {
            $this->error(self::NOT_VALID);
            return false;
        }

        $rounds = $this->_entityManager
            ->getRepository('QuizBundle\Entity\Round')
            ->findBy(
                array(
                    'quiz'=>$this->_quiz->getId(),
                    'order'=>$value
                )
            );

        if (count($rounds) == 0 || $rounds[0] == $this->_round)
            return true;

        $this->error(self::NOT_VALID);
        return false;
    }
}