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

namespace CommonBundle\Entity\General\Bank;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class VATRate
{
    /**
     * @var int The unit's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     */
    private $code;
    
    /**
     * @var float
     */
    private $rate;
    
    /**
     * A example: The VAT rate with code C has a rate of 0.21.
     * 
     * @param string $code
     * @param float $rate
     */
    public function __construct($code, $rate)
    {
        $this->code = $code;
        $this->rate = $rate;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function getRate()
    {
        return $this->rate;
    }
}
