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
 * Simple implementation of order.
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class SimpleOrder implements Order
{
    private $orderId;
    private $amount;
    private $languageCode;
    private $description;
    private $name;
    private $email;
    private $address;
    private $zip;
    private $town;
    private $countryCode;
    private $phoneNumber;
    
    public function __construct(
        $orderId,
        $amount,
        $languageCode,
        $description,
        $name,
        $email,
        $address,
        $zip,
        $town,
        $countryCode,
        $phoneNumber
    ) {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->languageCode = $languageCode;
        $this->description = $description;
        $this->name = $name;
        $this->email = $email;
        $this->address = $address;
        $this->zip = $zip;
        $this->town = $town;
        $this->countryCode = $countryCode;
        $this->phoneNumber = $phoneNumber;
        
    }
    
    public function getOrderId()
    {
        return $this->orderId;
    }
    
    public function get100TimesTheAmount()
    {
        return $this->amount;
    }
    
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    public function getDescription()
    {
        return $this->description;
    }
    
    public function getClientName()
    {
        return $this->name;
    }

    public function getClientEmail()
    {
        return $this->email;
    }
    
    public function getClientAddress()
    {
        return $this->address;
    }
    
    public function getClientZIP()
    {
        return $this->zip;
    }
    
    public function getClientTown()
    {
        return $this->town;
    }
    
    public function getClientCountryCode()
    {
        return $this->countryCode;
    }
    
    public function getClientPhoneNumber()
    {
        return $this->phoneNumber;
    }
}