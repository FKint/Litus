<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'controllers'  => array(
        'shift_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),

        'admin_shift' => array(
            '@common_jquery',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@admin_css',
            '@common_jquery_form',
            '@common_typeahead_remote',
        ),
        'admin_subscription' => array(
            '@common_jquery',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@admin_css',
            '@common_jquery_form',
            '@common_typeahead_remote',
        ),
        'admin_unit' => array(
            '@common_jquery',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@admin_css',
        ),

        'shift' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_responsive_css',
            '@site_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
        ),
    ),
    'routes' => array(),
);
