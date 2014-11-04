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

namespace CudiBundle\Component\Validator\Sale\Article\Restriction;

use CudiBundle\Entity\Sale\Article,
    Doctrine\ORM\EntityManager;

/**
 * Matches the given restriction against the database to check whether it already exists or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Exists extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var Article
     */
    private $_article;

    /**
     * @var EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The restriction already exist!',
    );

    /**
     * Create a new Restriction validator.
     *
     * @param Article       $article
     * @param EntityManager $entityManager
     * @param mixed         $opts          The validator's options
     */
    public function __construct(Article $article, EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_article = $article;
        $this->_entityManager = $entityManager;
    }

    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field is valid.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $restriction = null;
        if ('amount' == $value) {
            $restriction = $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sale\Article\Restriction\Amount')
                ->findOneByArticle($this->_article);
        } elseif ('member' == $value) {
            $restriction = $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sale\Article\Restriction\Member')
                ->findOneByArticle($this->_article);
        } elseif ('study' == $value) {
            $restriction = $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sale\Article\Restriction\Study')
                ->findOneByArticle($this->_article);
        }

        if (null === $restriction) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}