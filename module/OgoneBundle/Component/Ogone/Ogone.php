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

use OgoneBundle\Component\Ogone\Impl\FormGenerator,
    OgoneBundle\Component\Ogone\Impl\SHA512,
    OgoneBundle\Component\Ogone\Impl\FormSignatureCalculator,
    OgoneBundle\Component\Ogone\Impl\FormParameters\Register;

/**
 * The facade for the whole Ogone-support compontent. You should always
 * interact with an instance of this class. Interaction with classes in
 * the Impl sub-namespace is strongly discouraged.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Ogone
{
    /**
     * @var \OgoneBundle\Component\Ogone\Impl\FormGenerator
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {   
        $this->configuration = $configuration;
    }

    /**
     * Generates the form information for the order.
     * 
     * @param \OgoneBundle\Component\Ogone\Order $order
     * 
     * @return \OgoneBundle\Component\Ogone\FormInformation
     */
    public function generateFormInformation(Order $order)
    {
        $hashCalculator = new SHA512();
        $passphrase = $configuration->getSHAInPassphrase();
        $formSignatureCalculator =
            new FormSignatureCalculator($hashCalculator, $passphrase);
        $factory = new FormInformationFactory();
        $register = new Register();

        $formGenerator = new FormGenerator(
            $this->configuration,
            $formSignatureCalculator,
            $factory,
            $register
        );

        return $formGenerator->generate($order);
    }
}
