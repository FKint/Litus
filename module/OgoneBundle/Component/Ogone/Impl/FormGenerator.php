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

use OgoneBundle\Component\Ogone\Configuration,
    OgoneBundle\Component\Ogone\Order,
    OgoneBundle\Component\Ogone\Impl\FormParameters\Register;

/**
 * This class generates the FormInformation.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class FormGenerator
{
    /**
     * @var \OgoneBundle\Component\Ogone\Configuration
     */
    private $configuration;
    
    /**
     * @var \OgoneBundle\Component\Ogone\Impl\FormSignatureCalculator
     */
    private $formSignatureCalculator;
    
    /**
     * @var \OgoneBundle\Component\Ogone\Impl\FormInformationFactory
     */
    private $factory;
    
    /**
     * @var \OgoneBundle\Component\Ogone\Impl\FormParameters\Register
     */
    private $register;
    
    /**
     * C'tor.
     * 
     * @param \OgoneBundle\Component\Ogone\Configuration $configuration
     * @param \OgoneBundle\Component\Ogone\Impl\FormSignatureCalculator $formSignatureCalculator
     * @param \OgoneBundle\Component\Ogone\Impl\FormInformationFactory $factory
     * @param \OgoneBundle\Component\Ogone\Impl\FormParameters\Register $register
     */
    public function __construct(
        Configuration $configuration,
        FormSignatureCalculator $formSignatureCalculator,
        FormInformationFactory $factory,
        Register $register
    )
    {
        $this->configuration = $configuration;
        $this->formSignatureCalculator = $formSignatureCalculator;
        $this->factory = $factory;
        $this->register = $register;
    }
    
    /**
     * Generate the FormInformation.
     * 
     * @param \OgoneBundle\Component\Ogone\Order $order
     * 
     * @return \OgoneBundle\Component\Ogone\FormInformation
     */
    public function generate(Order $order)
    {
        if($this->configuration->isProductionEnvironment())
            $url = 'https://secure.ogone.com/ncol/prod/orderstandard.asp';
        else
            $url = 'https://secure.ogone.com/ncol/test/orderstandard.asp';
        
        $parameters = $this->register->createFormParametersIfValid(
            $order, $this->configuration);
        
        $signature = $this->formSignatureCalculator->calculate($parameters);
        $parameters['SHASIGN'] = $signature;
        
        return $this->factory->create($url, $parameters);
    }
}
