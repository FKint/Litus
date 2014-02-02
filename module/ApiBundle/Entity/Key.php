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

namespace ApiBundle\Entity;

use DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores an API key.
 *
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\Key")
 * @ORM\Table(name="api.keys")
 */
class Key
{
    /**
     * @var integer The ID of this code
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The expire time of this code
     *
     * @ORM\Column(name="expiration_time", type="datetime", nullable=true)
     */
    private $expirationTime;

    /**
     * @var string The host this key's valid for
     *
     * @ORM\Column(type="string")
     */
    private $host;

    /**
     * @var string The code
     *
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private $code;

    /**
     * @param string $host
     * @param string $code
     * @param int $expirationTime
     */
    public function __construct($host, $code, $expirationTime = 946080000)
    {
        $this->expirationTime = new DateTime(
            'now ' . (($expirationTime < 0) ? '-' : '+') . abs($expirationTime) . ' seconds'
        );

        $this->host = $host;
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return \ApiBundle\Entity\Key
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Revokes the API key.
     *
     * @return void
     */
    public function revoke()
    {
        $this->expirationTime = new DateTime();
    }

    /**
     * Checks whether or not this key is valid.
     *
     * @param string $ip The remote IP
     * @return boolean
     */
    public function validate($ip)
    {
        $now = new DateTime();
        if ($this->expirationTime < $now)
            return false;

        //if (gethostbyname($this->host) != $ip)
        //    return false;

        return true;
    }
}
