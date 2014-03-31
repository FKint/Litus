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

namespace GalleryBundle\Entity\Album;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the album item.
 *
 * @ORM\Entity(repositoryClass="GalleryBundle\Repository\Album\Album")
 * @ORM\Table(name="gallery.albums")
 */
class Album
{
    /**
     * @var int The ID of this album
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The time of creation of this album
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @var \CommonBundle\Entity\User\Person The person who created this album
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="create_person", referencedColumnName="id")
     */
    private $createPerson;

    /**
     * @var \DateTime The date the photo's of this album were created
     *
     * @ORM\Column(name="date_activity", type="datetime")
     */
    private $dateActivity;

    /**
     * @var array The translations of this album
     *
     * @ORM\OneToMany(targetEntity="GalleryBundle\Entity\Album\Translation", mappedBy="album", cascade={"remove"})
     */
    private $translations;

    /**
     * @var string The name of this album
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var array The photos of this album
     *
     * @ORM\OneToMany(targetEntity="GalleryBundle\Entity\Album\Photo", mappedBy="album", cascade={"remove"})
     * @ORM\OrderBy({"id": "ASC"})
     */
    private $photos;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param \DateTime                        $date
     */
    public function __construct(Person $person, DateTime $date)
    {
        $this->createTime = new DateTime();
        $this->createPerson = $person;
        $this->dateActivity = $date;
        $this->name = $date->format('d_m_Y_H_i');
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getCreatePerson()
    {
        return $this->createPerson;
    }

    /**
     * @param \DateTime $date
     *
     * @return \GalleryBundle\Entity\Album\Album
     */
    public function setDate(DateTime $date)
    {
        $this->dateActivity = $date;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->dateActivity;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return \GalleryBundle\Entity\Album\Translation
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        foreach ($this->translations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == \Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback)
            return $fallbackTranslation;

        return null;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return string
     */
    public function getTitle(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getTitle();

        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @return \GalleryBundle\Entity\Album\Photo
     */
    public function getPhoto()
    {
        do {
            $num = rand(0, count($this->photos) - 1);
        } while ($this->photos[$num]->isCensored());

        return $this->photos[$num];
    }

    /**
     *
     * @return Album
     */
    public function updateName()
    {
        $translation = $this->getTranslation();
        $this->name = $this->getDate()->format('d_m_Y_H_i_s') . '_' . \CommonBundle\Component\Util\Url::createSlug($translation->getTitle());

        return $this;
    }
}
