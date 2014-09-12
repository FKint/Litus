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

use OgoneBundle\Component\ParameterTransformer\Impl\FormSignatureCalculator,
    OgoneBundle\Component\ParameterTransformer\Impl\SHA512;

/**
 * The class calculates the signature for the parameters that are to be send
 * to Ogone. The pass phrase needs to be configured at Ogone.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class FormSignatureCalculatorTest extends \PHPUnit_Framework_TestCase
{
    public function testWithEchoHash()
    {
        $prase = 'Beer';
        $hashCalculator = new EchoHash();
        $formSigCal = new FormSignatureCalculator($hashCalculator, $prase);

        $parameters = array(
            'Bkey' => 34,
            'Akey' => 'Bel'
        );

        $hash = $formSigCal->calculate($parameters);
        $exp = 'AKEY=BelBeerBKEY=34Beer';
        $this->assertEquals($exp, $hash);
    }

    public function testNormalHash()
    {
        $prase = 'Beer';
        $hashCalculator = new SHA512();
        $formSigCal = new FormSignatureCalculator($hashCalculator, $prase);

        $parameters = array(
            'Bkey' => 34,
            'Akey' => 'Bel'
        );

        $hash = $formSigCal->calculate($parameters);
        $exp = '8DB31F5488513F045B85CD207700674B46678D0A767CC5A61BE4F3D1CCAD' .
        '1A5F6776965288E5933769E07824E503CC5E62860BBDC6A4E036342652E74F375E6F';

        $this->assertEquals(strtolower($exp), strtolower($hash));
    }
}
