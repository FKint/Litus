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

namespace OgoneBundle\Test\Component\Ogone\Impl;

use OgoneBundle\Test\Component\OgoneProtocol\SimpleTestConfiguration;
use OgoneBundle\Test\Component\OgoneProtocol\SimpleOrder;
use OgoneBundle\Component\OgoneProtocol\OgoneProtocol;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class OgoneProtocolTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateFormWithAllParameters()
    {
        $configuration = new SimpleTestConfiguration(
            'PSPId',
            'PassIn',
            'PassOut',
            'EUR'
        );
        
        $order = new SimpleOrder(
            '1',
            666,
            'SV',
            'Test',
            'Bambi',
            'google@google.com',
            'StudWijk Arenberg 6/1',
            '3001',
            'Heverlee',
            'SE',
            'MommyToldMeNotToShareMyNumber'
        );

        $ogone = new OgoneProtocol($configuration);
        $formInfo = $ogone->generateFormInformation($order);

        $expect = array(  
            'PSPID' => 'PSPId',
            'ORDERID' => '1',
            'AMOUNT' => '666',
            'CURRENCY' => 'EUR',
            'LANGUAGE' => 'SV',
            'CN' => 'Bambi',
            'EMAIL' => 'google@google.com',
            'OWNERZIP' => '3001',
            'OWNERTOWN' => 'Heverlee',
            'OWNERADDRESS' => 'StudWijk Arenberg 6/1',
            'OWNERCTY' => 'SE',
            'OWNERTELNO' => 'MommyToldMeNotToShareMyNumber',
            'COM' => 'Test',
            'SHASIGN' => 'b55aa900e0969548f9d203482c9834e6fa5c72b63118281888' .
                'cd50764739019e34ecf8b9b480e8d282de79e399a8aa0a6673954717c67' .
                'd6ac14f77651727378a'
            
        );
        
        $this->arrayTests($expect, $formInfo->getHiddenParameters());
    }
    
    public function testGenerateFormWithRequiredParameters()
    {
        $configuration = new SimpleTestConfiguration(
            'PSPId',
            'PassIn',
            null,
            'EUR'
        );
    
        $order = new SimpleOrder(
            '1',
            666,
            'SV',
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
    
        $ogone = new OgoneProtocol($configuration);
        $formInfo = $ogone->generateFormInformation($order);
    
        $expect = array(
            'PSPID' => 'PSPId',
            'ORDERID' => '1',
            'AMOUNT' => '666',
            'CURRENCY' => 'EUR',
            'LANGUAGE' => 'SV',
            'SHASIGN' => '823a4cd1f350748a727dfcc37f1f7bd392990fcea108c25260' .
                '49351fc05f3614a33cb546feadafd4fbc5aa3dcb3332389da73365cdded' .
                'e11b8122d4ff893d23e'
        );
    
        $this->arrayTests($expect, $formInfo->getHiddenParameters());
    }
    
    protected function arrayTests($expected, $actual)
    {
        ksort($expected);
        ksort($actual);
        
        $this->assertEquals($expected, $actual);
    }
}
