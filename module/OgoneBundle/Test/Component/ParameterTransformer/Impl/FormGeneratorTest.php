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

namespace OgoneBundle\Test\Component\ParameterTransformer\Impl;

use OgoneBundle\Component\ParameterTransformer\Impl\FormGenerator;
use OgoneBundle\Component\ParameterTransformer\Impl\FormInformationFactory;
use OgoneBundle\Test\Component\ParameterTransformer\TestEnvironmentConfiguration;
use OgoneBundle\Test\Component\ParameterTransformer\ProductionEnvironmentConfiguration;
use OgoneBundle\Test\Component\ParameterTransformer\AllNullOrder;
use OgoneBundle\Test\Component\ParameterTransformer\Impl\FormParameters\FixedRegister;
use OgoneBundle\Test\Component\ParameterTransformer\Impl\FormParameters\AlwaysFailRegister;

/**
 * The class calculates the signature for the parameters that are to be send
 * to Ogone. The pass phrase needs to be configured at Ogone.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class FormGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $configuration = new TestEnvironmentConfiguration();
        $fixedSignature = new FixedSignature('Signature');
        $factory = new FormInformationFactory();
        $register = new FixedRegister(array('B' => 'R', 'C' => 'D'));
        $order = new AllNullOrder();

        $formGenerator = new FormGenerator(
            $configuration,
            $fixedSignature,
            $factory,
            $register
        );

        $formInfo = $formGenerator->generate($order);

        $this->assertEquals(
            'https://secure.ogone.com/ncol/test/orderstandard.asp',
            $formInfo->getActionUrl()
        );

        $exp = array(
            'B' => 'R',
            'C' => 'D',
            'SHASIGN' => 'Signature',
        );

        $this->arrayTests($exp, $formInfo->getHiddenParameters());
    }

    public function testProductionUrl()
    {
        $configuration = new ProductionEnvironmentConfiguration();
        $fixedSignature = new FixedSignature('Signature');
        $factory = new FormInformationFactory();
        $register = new FixedRegister(array('B' => 'R', 'C' => 'D'));
        $order = new AllNullOrder();

        $formGenerator = new FormGenerator(
            $configuration,
            $fixedSignature,
            $factory,
            $register
        );

        $formInfo = $formGenerator->generate($order);

        $this->assertEquals(
            'https://secure.ogone.com/ncol/prod/orderstandard.asp',
            $formInfo->getActionUrl());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidParameter()
    {
        $configuration = new ProductionEnvironmentConfiguration();
        $fixedSignature = new FixedSignature('Signature');
        $factory = new FormInformationFactory();
        $register = new AlwaysFailRegister();
        $order = new AllNullOrder();
        
        $formGenerator = new FormGenerator(
            $configuration,
            $fixedSignature,
            $factory,
            $register
        );
        
        $formInfo = $formGenerator->generate($order);
    }
    
    protected function arrayTests($expected, $actual)
    {
        ksort($expected);
        ksort($actual);

        $this->assertEquals($expected, $actual);
    }
}