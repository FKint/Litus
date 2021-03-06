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
    'secretarybundle' => array(
        'secretary_registration' => array(
            'add', 'complete', 'edit', 'saveStudies', 'saveSubjects', 'studies', 'subjects',
        ),
        'secretary_admin_registration' => array(
            'add', 'barcode', 'cancel', 'edit', 'manage', 'search',
        ),
        'secretary_admin_export' => array(
            'download', 'export',
        ),
        'secretary_admin_photos' => array(
            'download', 'photos',
        ),
        'secretary_admin_promotion' => array(
            'add', 'delete', 'manage', 'search', 'update',
        ),
        'secretary_admin_working_group' => array(
            'manage', 'add', 'delete', 'search',
        ),
    ),
);
