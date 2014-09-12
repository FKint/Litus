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

namespace OgoneBundle\Test\Component\ParameterTransformer;

use OgoneBundle\Component\ParameterTransformer\Order;

/**
 * An order. With all field equals null.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class AllNullOrder implements Order
{
    public function getOrderId()
    {
        return null;
    }

    public function get100TimesTheAmount()
    {
        return null;
    }

    public function getLanguageCode()
    {
        return null;
    }

    public function getDescription()
    {
        return null;
    }

    public function getClientName()
    {
        return null;
    }

    public function getClientEmail()
    {
        return null;
    }

    public function getClientAddress()
    {
        return null;
    }

    public function getClientZIP()
    {
        return null;
    }

    public function getClientTown()
    {
        return null;
    }

    public function getClientCountryCode()
    {
        return null;
    }

    public function getClientPhoneNumber()
    {
        return null;
    }
}
