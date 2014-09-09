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

namespace OgoneBundle\Component\Ogone\Impl;

use OgoneBundle\Component\Ogone\HashCalculator;

class FormSignatureCalculator
{
    /**
     * @var string
     */
    private $passphrase;
    
    /**
     * 
     * @var HashCalculator
     */
    private $hashCal;
    
    public function __construct(HashCalculator $hashCalculator, string $passphrase)
    {
        $this->passphrase = $passphrase;
        $this->hashCal = $hashCalculator;
    }
    
    public function calculate($parametersToHash)
    {
        ksort($parametersToHash, SORT_STRING);
        
        $string = '';
        
        foreach($parametersToHash as $key => $val)
        {
            $string .= strtoupper($key) . $val . $this->passphrase;
        }
        
        return $this->hashCal->hash($string);
    }
}
