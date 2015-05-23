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

namespace CommonBundle\Entity\General;

use Doctrine\ORM\Mapping as ORM,
    InvalidArgumentException;

/**
 * This class represents a configuration entry that is saved in the database
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Config")
 * @ORM\Table(name="general.config")
 */
class Config
{
    /**
     * @static
     * @var string The separator used to denote the bundles
     */
    public static $separator = '.';

    /**
     * @var string The entry's key
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $key;

    /**
     * @var string The entry's value
     *
     * @ORM\Column(type="text")
     */
    private $value;

    /**
     * @var string A description for this configuration entry
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @var boolean Whether this entry is published
     *
     * @ORM\Column(type="boolean")
     */
    private $published;

    /**
     * @param  string                   $key   The entry's key
     * @param  string|array             $value The entry's value
     * @throws InvalidArgumentException Key must be a string
     */
    public function __construct($key, $value)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('Key must be a string');
        }

        $this->key = $key;
        $this->setValue($value);
        $this->published = false;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param  string|array $value The entry's value
     * @return self
     */
    public function setValue($value)
    {
        if (!is_string($value)) {
            $value = serialize($value);
        }

        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
    * @param  string|null $description A description for this configuration entry
    * @return self
    * @throws InvalidArgumentException Description must be a string or null
     */
    public function setDescription($description = null)
    {
        if (($description !== null) && !is_string($description)) {
            throw new InvalidArgumentException('Description must be a string or null');
        }

        $this->description = $description;

        return $this;
    }

    /**
     * @param  boolean $published
     * @return self
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }
}
