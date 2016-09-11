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

namespace CudiBundle\Hydrator\Isic;

class Order extends \CommonBundle\Component\Hydrator\Hydrator
{

    protected $entity = 'CommonBundle\Entity\User\Person\Academic';

    protected static $stdKeysPersonal = array(
        'first_name',
        'last_name',
        'sex',
    );

    protected static $stdKeysContact = array(
        'email',
        'phone_number',
    );

    private function convertBase64($file)
    {
        $type = pathinfo($file['name'], PATHINFO_EXTENSION);
        $data = file_get_contents($file['tmp_name']);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $base64;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = array();
        }

        $object['ISICCardNumber'] = '';
        $object['FirstName'] = $data['personal_info']['first_name'];
        $object['LastName'] = $data['personal_info']['last_name'];
        $object['BirthDate'] = $data['personal_info']['birthday'];
        $object['BirthPlace'] = $data['personal_info']['birthplace'];
        $object['Gender'] = $data['personal_info']['sex'];
        $object['Language'] = $data['personal_info']['language'];
        $object['Street'] = $data['address']['street'] . ' ' . $data['address']['number'];
        $object['PostalCode'] = $data['address']['postal'];
        $object['City'] = $data['address']['city'];
        $object['Email'] = $data['contact_details']['email'];
        $object['PhoneNumber'] = $data['contact_details']['phone_number'];
        $object['Course'] = $data['studies']['course'];
        $object['School'] = $data['studies']['school'];
        $object['StudentCity'] = $data['studies']['student_city'];
        $object['Year'] = $data['studies']['year'];
        $object['Optin'] = $data['optins']['newsletter'] == true ? '1' : '0';
        $object['PostOptOut'] = $data['optins']['post'] == true ? '1' : '0';
        $object['PostOptOutThird'] = $data['optins']['post_third'] == true ? '1' : '0';

        $object['Photo'] = $this->convertBase64($data['photo_group']['photo']);
        $object['ImageExtension'] = pathinfo($data['photo_group']['photo']['name'], PATHINFO_EXTENSION);

        return $object;
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = array();

        $data['personal_info'] = $this->stdExtract($object, array(self::$stdKeysPersonal));
        $data['contact_details'] = $this->stdExtract($object, array(self::$stdKeysContact));

        $data['personal_info']['birthday'] = $object->getBirthday() !== null
            ? $object->getBirthday()->format('d/m/Y')
            : '';

        $hydratorAddress = $this->getHydrator('CommonBundle\Hydrator\General\Address');
        $data['address'] = $hydratorAddress->extract($object->getSecondaryAddress());

        return $data;
    }
}