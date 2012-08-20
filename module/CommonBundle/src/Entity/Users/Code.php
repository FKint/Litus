<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\Users;

use DateTime;

/**
 * This entity stores a user's codes.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Users\Code")
 * @Table(name="users.codes")
 */
class Code
{
    /**
     * @var integer The ID of this code
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The expire time of this code
     *
     * @Column(name="expiration_time", type="datetime", nullable=true)
     */
    private $expirationTime;

    /**
     * @var string The code
     *
     * @Column(type="string", length=32, unique=true)
     */
    private $code;

    /**
     * @param string $code
     * @param int $expirationTime
     */
    public function __construct($code, $expirationTime = 1800)
    {
        $this->expirationTime = new DateTime(
            'now ' . (($expirationTime < 0) ? '-' : '+') . abs($expirationTime) . ' seconds'
        );

        $this->code = $code;
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
    public function getCode()
    {
        return $this->code;
    }
}
