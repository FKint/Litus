<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace BrBundle\Controller\Admin;

use Exception;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'br.logo_path',
                    'value'       => 'data/br/companies',
                    'description' => 'The path to the company logo files',
                ),
                array(
                    'key'         => 'br.file_path',
                    'value'       => 'data/br/files',
                    'description' => 'The path to the company files',
                ),
                array(
                    'key'         => 'br.public_logo_path',
                    'value'       => '_br/img',
                    'description' => 'The path to the public company logo files',
                ),
                array(
                    'key'         => 'br.pdf_generator_path',
                    'value'       => 'data/br/pdf_generator',
                    'description' => 'The path to the PDF generator files',
                ),
                array(
                    'key'         => 'br.cv_book_open',
                    'value'       => '0',
                    'description' => 'Whether the CV Book is currently open for entries or not',
                ),
                array(
                    'key'         => 'br.account_activated_mail',
                    'value'       => 'Dear {{ name }},

A corporate account for you was created on VTK with username {{ username }}.
Click here to activate it: http://litus/account/activate/code/{{ code }}
You can use this account to view the CV Book at http://litus/corporate

With best regards,
The VTK Corporate Team',
                    'description' => 'The email sent when an account is activated',
                ),
                array(
                    'key'         => 'br.account_activated_subject',
                    'value'       => 'VTK Corporate Account',
                    'description' => 'The mail subject when an account is activated',
                ),
                array(
                    'key'         => 'br.cv_book_language',
                    'value'       => 'nl',
                    'description' => 'The language used in the printed version of the CV Book',
                ),
                array(
                    'key'         => 'br.cv_book_foreword',
                    'value'       => '<section title="Example section">
    <content>
        Example content of this section.
    </content>
</section>',
                    'description' => 'The foreword of the CV Book',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'brbundle' => array(
                    'br_admin_company' => array(
                        'add', 'delete', 'editLogo', 'edit', 'logo', 'manage'
                    ),
                    'br_admin_company_event' => array(
                        'add', 'delete', 'edit', 'editPoster', 'manage', 'progress', 'upload'
                    ),
                    'br_admin_company_job' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'br_admin_company_user' => array(
                        'add', 'delete', 'edit', 'manage', 'activate'
                    ),
                    'br_admin_company_logo' => array(
                        'manage', 'add', 'delete'
                    ),
                    'br_admin_cv_entry' => array(
                        'manage', 'delete', 'export', 'exportAcademics'
                    ),

                    'br_career_index' => array(
                        'index'
                    ),
                    'br_career_company' => array(
                        'view', 'overview', 'file', 'search', 'logo',
                    ),
                    'br_career_event' => array(
                        'view', 'overview'
                    ),
                    'br_career_vacancy' => array(
                        'view', 'overview'
                    ),
                    'br_career_internship' => array(
                        'view', 'overview'
                    ),

                    'br_cv_index' => array(
                        'cv', 'edit', 'complete',
                    ),

                    'br_corporate_auth' => array(
                        'login', 'logout',
                    ),
                    'br_corporate_index' => array(
                        'index',
                    ),
                    'br_corporate_cv' => array(
                        'grouped', 'list', 'search', 'cvPhoto',
                    ),
                )
            )
        );

        $this->installRoles(
            array(
                'corporate' => array(
                    'system' => true,
                    'parents' => array(
                        'guest',
                    ),
                    'actions' => array(
                    ),
                ),
            )
        );
    }
}
