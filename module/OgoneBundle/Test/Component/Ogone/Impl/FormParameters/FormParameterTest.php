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

namespace OgoneBundle\Test\Compontent\Ogone\Impl\FormParameters;

use OgoneBundle\Component\Ogone\Impl\FormParameters\Amount;
use OgoneBundle\Test\Component\Ogone\TestEnvironmentConfiguration;
use OgoneBundle\Test\Component\Ogone\AmountOrder;
use OgoneBundle\Test\Component\Ogone\AllNullOrder;
use OgoneBundle\Component\Ogone\Impl\FormParameters\Name;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class FormParameterTest extends \PHPUnit_Framework_TestCase
{
    public function testMaxValidLength()
    {
        $amount = new Amount();
        $array = array();
        $config = new TestEnvironmentConfiguration();
        $order = new AmountOrder(123456789012345);
        
        $amount->addToArrayIfValid($array, $config, $order);
        
        $this->assertEquals(1, count($array));
        $this->assertEquals('123456789012345', $array['AMOUNT']);
    }
    
    public function testNullButOptional()
    {
        $amount = new Name();
        $array = array();
        $config = new TestEnvironmentConfiguration();
        $order = new AllNullOrder();
    
        $amount->addToArrayIfValid($array, $config, $order);
        
        $this->assertEquals(0, count($array));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTooLong()
    {
        $amount = new Amount();
        $array = array();
        $config = new TestEnvironmentConfiguration();
        $order = new AmountOrder(1234567890123456);
        
        $amount->addToArrayIfValid($array, $config, $order);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidType()
    {
        $amount = new Amount();
        $array = array();
        $config = new TestEnvironmentConfiguration();
        $order = new AmountOrder('123456789012345');
        
        $amount->addToArrayIfValid($array, $config, $order);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNullAndMandatory()
    {
        $amount = new Amount();
        $array = array();
        $config = new TestEnvironmentConfiguration();
        $order = new AllNullOrder();
        
        $amount->addToArrayIfValid($array, $config, $order);
    }
}