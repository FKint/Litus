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
    'sportbundle' => array(
        'sport_admin_run' => array(
            'edit', 'editGroup', 'departments', 'groups', 'identification', 'killSocket', 'laps', 'queue', 'reward', 'runner', 'update', 'next', 'delete','editSpeedyGroup',
        ),
        'sport_run_group' => array(
            'add', 'getName',
        ),
        'sport_run_index' => array(
            'index',
        ),
        'sport_run_queue' => array(
            'index', 'getName',
        ),
        'sport_run_screen' => array(
            'index',
        ),
        'sport_run_screen_outside' => array(
            'index',
        ),
    ),
);
