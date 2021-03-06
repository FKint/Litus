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

namespace BrBundle\Form\Admin\Company\User;

/**
 * Add User
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Form\Admin\Person\Add
{
    protected $hydrator = 'BrBundle\Hydrator\User\Person\Corporate';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'activate',
            'label'      => 'Activation Mail',
            'value'         => true,
        ));

        $this->remove('roles');

        $this->remove('submit')
            ->addSubmit('Add', 'user_add');
    }
}
