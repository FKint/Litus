<?php
// Generated by ZF2's ./bin/classmap_generator.php
return array(
    'BrBundle\Module'                                   => __DIR__ . '/Module.php',
    'BrBundle\Repository\Company\Event'                 => __DIR__ . '/src/Repository/Company/Event.php',
    'BrBundle\Repository\Company\Page'                  => __DIR__ . '/src/Repository/Company/Page.php',
    'BrBundle\Repository\Company\Job'                   => __DIR__ . '/src/Repository/Company/Job.php',
    'BrBundle\Repository\Company\Logo'                  => __DIR__ . '/src/Repository/Company/Logo.php',
    'BrBundle\Repository\Company'                       => __DIR__ . '/src/Repository/Company.php',
    'BrBundle\Repository\Cv\Entry'                      => __DIR__ . '/src/Repository/Cv/Entry.php',
    'BrBundle\Repository\Cv\Language'                   => __DIR__ . '/src/Repository/Cv/Language.php',
    'BrBundle\Repository\Users\People\Corporate'        => __DIR__ . '/src/Repository/Users/People/Corporate.php',
    'BrBundle\Repository\Users\Statuses\Corporate'      => __DIR__ . '/src/Repository/Users/Statuses/Corporate.php',
    'BrBundle\Repository\Contract'                      => __DIR__ . '/src/Repository/Contract.php',
    'BrBundle\Repository\Contracts\Section'             => __DIR__ . '/src/Repository/Contracts/Section.php',
    'BrBundle\Repository\Contracts\Composition'         => __DIR__ . '/src/Repository/Contracts/Composition.php',
    'BrBundle\Form\Admin\Company\User\Add'              => __DIR__ . '/src/Form/Admin/Company/User/Add.php',
    'BrBundle\Form\Admin\Company\User\Edit'             => __DIR__ . '/src/Form/Admin/Company/User/Edit.php',
    'BrBundle\Form\Admin\Company\Add'                   => __DIR__ . '/src/Form/Admin/Company/Add.php',
    'BrBundle\Form\Admin\Company\Job\Add'               => __DIR__ . '/src/Form/Admin/Company/Job/Add.php',
    'BrBundle\Form\Admin\Company\Job\Edit'              => __DIR__ . '/src/Form/Admin/Company/Job/Edit.php',
    'BrBundle\Form\Admin\Company\Logo\Add'              => __DIR__ . '/src/Form/Admin/Company/Logo/Add.php',
    'BrBundle\Form\Admin\Company\Edit'                  => __DIR__ . '/src/Form/Admin/Company/Edit.php',
    'BrBundle\Form\Admin\Company\Logo'                  => __DIR__ . '/src/Form/Admin/Company/Logo.php',
    'BrBundle\Form\Admin\Section\Add'                   => __DIR__ . '/src/Form/Admin/Section/Add.php',
    'Admin\Form\Section\Edit'                           => __DIR__ . '/src/Form/Admin/Section/Edit.php',
    'Admin\Form\Contract\Add'                           => __DIR__ . '/src/Form/Admin/Contract/Add.php',
    'Admin\Form\Contract\Edit'                          => __DIR__ . '/src/Form/Admin/Contract/Edit.php',
    'BrBundle\Form\Cv\Add'                              => __DIR__ . '/src/Form/Cv/Add.php',
    'BrBundle\Form\Cv\Edit'                             => __DIR__ . '/src/Form/Cv/Edit.php',
    'BrBundle\Component\Document\Pdf\ContractGenerator' => __DIR__ . '/src/Component/Document/Generator/Pdf/Contract.php',
    'Litus\Br\LetterGenerator'                          => __DIR__ . '/src/Component/Document/Generator/Pdf/Letter.php',
    'Litus\Br\InvoiceGenerator'                         => __DIR__ . '/src/Component/Document/Generator/Pdf/Invoice.php',
    'BrBundle\Component\Validator\FieldLength'          => __DIR__ . '/src/Component/Validator/FieldLength.php',
    'BrBundle\Component\Validator\Logo\Type'            => __DIR__ . '/src/Component/Validator/Logo/TypeValidator.php',
    'BrBundle\Component\Validator\CompanyName'          => __DIR__ . '/src/Component/Validator/CompanyName.php',
    'BrBundle\Component\Controller\CareerController'    => __DIR__ . '/src/Component/Controller/CareerController.php',
    'BrBundle\Component\Controller\CorporateController' => __DIR__ . '/src/Component/Controller/CorporateController.php',
    'BrBundle\Component\Controller\CvController'        => __DIR__ . '/src/Component/Controller/CvController.php',
    'BrBundle\Entity\Company\Event'                     => __DIR__ . '/src/Entity/Company/Event.php',
    'BrBundle\Entity\Company\Page'                      => __DIR__ . '/src/Entity/Company/Page.php',
    'BrBundle\Entity\Company\Job'                       => __DIR__ . '/src/Entity/Company/Job.php',
    'BrBundle\Entity\Company\Logo'                      => __DIR__ . '/src/Entity/Company/Logo.php',
    'BrBundle\Entity\Company'                           => __DIR__ . '/src/Entity/Company.php',
    'BrBundle\Entity\Cv\Entry'                          => __DIR__ . '/src/Entity/Cv/Entry.php',
    'BrBundle\Entity\Cv\Language'                       => __DIR__ . '/src/Entity/Cv/Language.php',
    'BrBundle\Entity\Users\People\Corporate'            => __DIR__ . '/src/Entity/Users/People/Corporate.php',
    'BrBundle\Entity\Users\Statuses\Corporate'          => __DIR__ . '/src/Entity/Users/Statuses/Corporate.php',
    'BrBundle\Entity\Contract'                          => __DIR__ . '/src/Entity/Contract.php',
    'BrBundle\Entity\Contracts\Section'                 => __DIR__ . '/src/Entity/Contracts/Section.php',
    'BrBundle\Entity\Contracts\Composition'             => __DIR__ . '/src/Entity/Contracts/Composition.php',
    'BrBundle\Controller\Admin\SectionController'       => __DIR__ . '/src/Controller/Admin/SectionController.php',
    'Admin\ContractController'                          => __DIR__ . '/src/Controller/Admin/ContractController.php',
    'BrBundle\Controller\Admin\Company\UserController'  => __DIR__ . '/src/Controller/Admin/Company/UserController.php',
    'BrBundle\Controller\Admin\Company\JobController'   => __DIR__ . '/src/Controller/Admin/Company/JobController.php',
    'BrBundle\Controller\Admin\Company\EventController' => __DIR__ . '/src/Controller/Admin/Company/EventController.php',
    'BrBundle\Controller\Admin\Company\LogoController'  => __DIR__ . '/src/Controller/Admin/Company/LogoController.php',
    'BrBundle\Controller\Admin\InstallController'       => __DIR__ . '/src/Controller/Admin/InstallController.php',
    'BrBundle\Controller\Admin\CompanyController'       => __DIR__ . '/src/Controller/Admin/CompanyController.php',
    'BrBundle\Controller\Admin\CvController'            => __DIR__ . '/src/Controller/Admin/CvController.php',
    'BrBundle\Controller\Career\CompanyController'      => __DIR__ . '/src/Controller/Career/CompanyController.php',
    'BrBundle\Controller\Career\VacancyController'      => __DIR__ . '/src/Controller/Career/VacancyController.php',
    'BrBundle\Controller\Career\IndexController'        => __DIR__ . '/src/Controller/Career/IndexController.php',
    'BrBundle\Controller\Career\InternshipController'   => __DIR__ . '/src/Controller/Career/InternshipController.php',
    'BrBundle\Controller\Career\EventController'        => __DIR__ . '/src/Controller/Career/EventController.php',
    'BrBundle\Controller\Corporate\IndexController'     => __DIR__ . '/src/Controller/Corporate/IndexController.php',
    'BrBundle\Controller\Corporate\AuthController'      => __DIR__ . '/src/Controller/Corporate/AuthController.php',
    'BrBundle\Controller\Corporate\CvController'        => __DIR__ . '/src/Controller/Corporate/CvController.php',
    'BrBundle\Controller\CvController'                  => __DIR__ . '/src/Controller/CvController.php',
);