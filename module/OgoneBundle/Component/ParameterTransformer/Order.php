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

namespace OgoneBundle\Component\ParameterTransformer;

/**
 * An order. An implementation of this interface will typically interact
 * with entities.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
interface Order
{
    /**
     * Mandatory and must be unique. This value will be used to prevent the
     * user from paying twice.  Max length: 30.
     *
     * @return string
     */
    public function getOrderId();

    /**
     * The amount multiplied by 100. Mandatory. Max length: 15.
     *
     * @return integer
     */
    public function get100TimesTheAmount();

    /**
     * Mandatory, ISO standard. Max length: 2.
     *
     * @return string
     */
    public function getLanguageCode();

    /**
     * Optional, usefull for customer. This description can be showed on
     * bank statements. Max length: 100.
     *
     * @return null|string
     */
    public function getDescription();

    /**
     * Optional, usefull for combating fraud. Max length: 35.
     *
     * @return null|string
     */
    public function getClientName();

    /**
     * Optional, usefull for combating fraud. Max length: 50.
     *
     * @return null|string
    */
    public function getClientEmail();

    /**
     * Optional, usefull for combating fraud. Max length: 35.
     *
     * @return null|string
    */
    public function getClientAddress();

    /**
     * Optional, usefull for combating fraud. Max length: 10.
     *
     * @return null|string
    */
    public function getClientZIP();

    /**
     * Optional, usefull for combating fraud. Max length: 40.
     *
     * @return null|string
    */
    public function getClientTown();

    /**
     * Country in ISO 3166-1-alpha-2. Optional, usefull for combating fraud.
     * Max length: 2.
     *
     * @return null|string
    */
    public function getClientCountryCode();

    /**
     * Special characters (+ or/ for instance) are allowed. Optional, usefull
     * for combating fraud. Max length: 30.
     *
     * @return null|string
    */
    public function getClientPhoneNumber();
}
