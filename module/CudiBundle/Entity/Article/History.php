<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CudiBundle\Entity\Article;

use CudiBundle\Entity\Article,
    CudiBundle\Entity\Article\SubjectMap as SubjectMapping,
    CudiBundle\Entity\Comment\Mapping as CommentMapping,
    CudiBundle\Entity\File\Mapping as FileMapping,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Article\History")
 * @ORM\Table(name="cudi.articles_history")
 */
class History
{
    /**
     * @var integer The ID of this article history
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CudiBundle\Entity\Article The newest version of the two
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var \CudiBundle\Entity\Article The oldest version of the two
     *
     * @ORM\OneToOne(targetEntity="CudiBundle\Entity\Article", cascade={"persist"})
     * @ORM\JoinColumn(name="precursor", referencedColumnName="id")
     */
    private $precursor;

    /**
     * @param \CudiBundle\Entity\Article $article The new version of the article
     * @param \CudiBundle\Entity\Article|null $precursor The old version of the article
     */
    public function __construct(Article $article, Article $precursor = null)
    {
        $this->precursor = isset($precursor) ? $precursor : $article->duplicate();

        $this->precursor->setVersionNumber($article->getVersionNumber())
            ->setIsHistory(true);

        if ($this->precursor->getTimeStamp() > $article->getTimeStamp()) {
            $date = $article->getTimeStamp();
            $article->setTimeStamp($this->precursor->getTimeStamp());
            $this->precursor->setTimeStamp($date);
        }

        $article->setVersionNumber($article->getVersionNumber()+1);

        $this->article = $article;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CudiBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param \CudiBundle\Entity\Article $article
     *
     * @return \CudiBundle\Entity\Article\History
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Article
     */
    public function getPrecursor()
    {
        return $this->precursor;
    }
}
