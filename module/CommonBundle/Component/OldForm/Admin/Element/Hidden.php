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

namespace CommonBundle\Component\OldForm\Admin\Element;

/**
 * Hidden form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Hidden extends \Zend\Form\Element\Hidden
{
    /**
     * @param  null|int|string                    $name    Optional name for the element
     * @param  array                              $options Optional options for the element
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($name, $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('id', $name);
    }
}