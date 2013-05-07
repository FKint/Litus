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

namespace PageBundle\Component\Validator;

use CommonBundle\Component\Util\Url,
    Doctrine\ORM\EntityManager;

/**
 * Matches the given page title against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Title extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var string The name exluded from this check
     */
    private $_exclude = '';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'There already exists a page with this title and the same parent'
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string The name exluded from this check
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, $exclude = '', $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_exclude = $exclude;
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $realParent = null;
        $parentName = null;
        if (isset($context['parent_' . $context['category']]) && '' != $context['parent_' . $context['category']]) {
            $realParent = $this->_entityManager
                ->getRepository('PageBundle\Entity\Nodes\Page')
                ->findOneById($context['parent_' . $context['category']]);

            $parentName = $realParent->getName();
        }

        $page = $this->_entityManager
            ->getRepository('PageBundle\Entity\Nodes\Page')
            ->findOneByNames(
                Url::createSlug($value), Url::createSlug($parentName)
            );

        if (null === $page || $page->getName() == $this->_exclude)
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}