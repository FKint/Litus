<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Validator;

/**
 * Verifies whether the given value is in a valid price format.
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 */
class Price extends \Zend\Validator\Regex
{
    function __construct() {
        parent::__construct('/^[0-9]+.?[0-9]{0,2}$/');

        parent::setMessage(
            'The given price is not valid'
        );
    }
}
