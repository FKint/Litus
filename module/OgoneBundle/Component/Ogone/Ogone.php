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

namespace OgoneBundle\Component\Ogone;

use OgoneBundle\Component\Ogone\Impl\FormGenerator;
use OgoneBundle\Component\Ogone\Impl\SHA512;
use OgoneBundle\Component\Ogone\Impl\FormSignatureCalculator;
use OgoneBundle\Component\Ogone\Impl\FormParameters\Register;

class Ogone
{
    /**
     * @var FormGenerator
     */
    private $configuration;
    
    public function __construct(Configuration $configuration)
    {   
        $this->configuration = $configuration;
    }
    
    public function generateFormInformation(Order $order)
    {
        $hashCalculator = new SHA512();
        $passphrase = $configuration->getSHAInPassphrase();
        $formSignatureCalculator =
            new FormSignatureCalculator($hashCalculator, $passphrase);
        $factory = new FormInformationFactory();
        $register = new Register();
        
        $formGenerator = new FormGenerator($this->configuration,
            $formSignatureCalculator, $factory, $register);
        
        return $formGenerator->generate($order);
    }
}
