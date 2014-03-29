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
    'submenus' => array(
        'Corporate Relations' => array(
            'subtitle' => array('Companies','CVs', 'Products'),
            'items'    => array(
                'br_admin_company'  => 'Companies',
                'br_admin_contract' => 'Contracts',
                'br_admin_cv_entry' => 'CVs',
                'br_admin_invoice'  => 'Invoices',
                'br_admin_order'    => 'Orders',
                'br_admin_overview' => 'Overview',
                'br_admin_product'  => 'Products',
            ),
        ),
    ),
);
