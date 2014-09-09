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

/**
 * All system-wide configurations for Ogone.
 * 
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
interface Configuration
{
    /**
     * Your Payment Service Provider ID at Ogone. Max length: 30.
     * 
     * @return string
     */
    public function getPspId();

    /**
     * The passphrase that is used to calculate the SHA hash when we send
     * a client to Ogone to pay.
     * 
     * @return string
     */
    public function getShaInPassphrase();

    /**
     * The passphrase that is used to calculate the SHA hash when the client
     * is sent back to our website.
     *
     * @return string
     */
    public function getShaOutPassphrase();

    /**
     * ISO standard. eg. EUR, USD, SEK. Max length 3.
     * 
     * @return string
     */
    public function getCurrencyCode();

    /**
     * @return bool
     */
    public function isProductionEnvironment();
}
