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

return array(
    'install.all'             => 'CommonBundle\Command\AllInstall',
    'install.common'          => 'CommonBundle\Command\Install',

    'common.gc'               => 'CommonBundle\Command\GarbageCollect',
    'common.config'           => 'CommonBundle\Command\Config',
    'common.test-config'      => 'CommonBundle\Command\TestConfig',
    'common.destroy-account'  => 'CommonBundle\Command\DestroyAccount',
    'common.acl-cleanup'      => 'CommonBundle\Command\AclCleanup',

    'assetic.build'           => 'CommonBundle\Command\Assetic\Build',
    'assetic.setup'           => 'CommonBundle\Command\Assetic\Setup',
);
