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

namespace StoreBundle\Controller\Admin;

use CommonBundle\Entity\General\Language;

/**
 * InstallController
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        /*$this->installConfig(
            array(
                array(
                    'key'         => 'news.rss_title',
                    'value'       => 'Vlaamse Technische Kring',
                    'description' => 'The title of the RSS feed',
                ),
                array(
                    'key'         => 'news.rss_description',
                    'value'       => serialize(
                        array(
                            'nl' => 'RSS Feed van de Vlaamse Technische Kring',
                            'en' => 'RSS Feed of the Vlaamse Technische Kring',
                        )
                    ),
                    'description' => 'The description of the RSS feed',
                ),
                array(
                    'key'         => 'news.rss_image_link',
                    'value'       => '/_site/img/logo.png',
                    'description' => 'The image of the RSS feed',
                ),
            )
        );*/
    }

    protected function initAcl()
    {
        /*$this->installAcl(
            array(
                'newsbundle' => array(
                    'news_admin_news' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'news' => array(
                        'feed', 'overview', 'view'
                    ),
                ),
            )
        );

        $this->installRoles(
            array(
                'guest' => array(
                    'parent_roles' => array(),
                    'actions' => array(
                        'news' => array(
                            'overview', 'view'
                        ),
                    ),
                ),
            )
        );*/
    }
}