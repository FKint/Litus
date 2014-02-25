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

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\Document\Generator\Csv as CsvGenerator,
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    CommonBundle\Entity\User\Person\Organization\AcademicYearMap,
    CommonBundle\Entity\User\Barcode,
    DateInterval,
    DateTime,
    SecretaryBundle\Component\Registration\Articles as RegistrationArticles,
    SecretaryBundle\Entity\Organization\MetaData,
    SecretaryBundle\Entity\Registration,
    SecretaryBundle\Form\Admin\Registration\Add as AddForm,
    SecretaryBundle\Form\Admin\Registration\Barcode as BarcodeForm,
    SecretaryBundle\Form\Admin\Registration\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * RegistrationController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RegistrationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $academicYear = $this->_getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $paginator = $this->paginator()->createFromEntity(
            'SecretaryBundle\Entity\Registration',
            $this->getParam('page'),
            array(
                'academicYear' => $academicYear,
            ),
            array(
                'timestamp' => 'ASC'
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
                'activeAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
                'organizations' => $organizations,
                'currentOrganization' => $this->_getOrganization(),
            )
        );
    }

    public function barcodeAction()
    {
        if (!($registration = $this->_getRegistration()))
            return new ViewModel();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $form = new BarcodeForm(
            $this->getEntityManager(), $registration->getAcademic()
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if (null !== $registration->getAcademic()->getBarcode()) {
                    if ($registration->getAcademic()->getBarcode()->getBarcode() != $formData['barcode']) {
                        $this->getEntityManager()->remove($registration->getAcademic()->getBarcode());
                        $this->getEntityManager()->persist(new Barcode($registration->getAcademic(), $formData['barcode']));
                    }
                } else {
                    $this->getEntityManager()->persist(new Barcode($registration->getAcademic(), $formData['barcode']));
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The barcode was successfully set!'
                    )
                );

                $this->redirect()->toRoute(
                    'secretary_admin_registration',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'registration' => $registration,
                'activeAcademicYear' => $registration->getAcademicYear(),
                'academicYears' => $academicYears,
                'form' => $form,
                'organizations' => $organizations,
                'currentOrganization' => $this->_getOrganization(),
            )
        );
    }

    public function addAction()
    {
        $academicYear = $this->_getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $academic = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['person_id']);

                $registration = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findOneByAcademicAndAcademicYear($academic, $academicYear);

                $organization = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization')
                    ->findOneById($formData['organization']);

                if (null !== $registration) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::WARNING,
                            'WARNING',
                            'There was already a registration for this academic!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'secretary_admin_registration',
                        array(
                            'action' => 'edit',
                            'id' => $registration->getId(),
                        )
                    );

                    return;
                }

                $metaData = new MetaData(
                    $academic,
                    $academicYear,
                    true,
                    $formData['irreeel'],
                    $formData['bakske'],
                    $formData['tshirt_size']
                );
                $this->getEntityManager()->persist($metaData);

                $organizationMap = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
                    ->findOneByAcademicAndAcademicYear($academic, $academicYear);

                if (null !== $organizationMap) {
                    $organizationMap->setOrganization($organization);
                } else {
                    $this->getEntityManager()->persist(new AcademicYearMap($academic, $academicYear, $organization));
                }

                RegistrationArticles::book(
                    $this->getEntityManager(),
                    $academic,
                    $organization,
                    $academicYear,
                    array(
                        'payed' => $formData['payed'],
                        'tshirtSize' => $formData['tshirt_size'],
                    )
                );

                $registration = new Registration(
                    $academic,
                    $this->getCurrentAcademicYear()
                );
                $registration->setPayed($formData['payed']);
                $this->getEntityManager()->persist($registration);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The registration was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'secretary_admin_registration',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'activeAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
                'organizations' => $organizations,
                'currentOrganization' => $this->_getOrganization(),
            )
        );
    }

    public function editAction()
    {
        if (!($registration = $this->_getRegistration()))
            return new ViewModel();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($registration->getAcademic(), $registration->getAcademicYear());

        $form = new EditForm($this->getEntityManager(), $registration, $metaData);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
            if ($formData['payed'] && $formData['cancel']) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'ERROR',
                            'The registration needs to be uncancelled before it can be payed !'
                        )
                    );
            }else if ($form->isValid()) {

                $registration->setPayed($formData['payed']);
                $registration->setCancelled(false);
                $organization = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization')
                    ->findOneById($formData['organization']);

                $organizationMap = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
                    ->findOneByAcademicAndAcademicYear($registration->getAcademic(), $registration->getAcademicYear());

                if (null !== $organizationMap) {
                    $organizationMap->setOrganization($organization);
                } else {
                    $this->getEntityManager()->persist(new AcademicYearMap($registration->getAcademic(), $registration->getAcademicYear(), $organization));
                }

                RegistrationArticles::book(
                    $this->getEntityManager(),
                    $registration->getAcademic(),
                    $organization,
                    $registration->getAcademicYear(),
                    array(
                        'payed' => $formData['payed'],
                        'tshirtSize' => $formData['tshirt_size'],
                    )
                );

                if (null === $metaData) {
                    $metaData = new MetaData(
                        $registration->getAcademic(),
                        $registration->getAcademicYear(),
                        false,
                        $formData['irreeel'],
                        $formData['bakske'],
                        $formData['tshirt_size']
                    );
                } else {
                    $metaData->setReceiveIrReeelAtCudi($formData['irreeel'])
                        ->setBakskeByMail($formData['bakske'])
                        ->setTshirtSize($formData['tshirt_size']);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The registration was successfully edited!'
                    )
                );

            }

            $this->redirect()->toRoute(
                    'secretary_admin_registration',
                    array(
                        'action' => 'edit',
                        'id' => $registration->getId(),
                    )
                );

            return new ViewModel();
        }

        return new ViewModel(
            array(
                'registration' => $registration,
                'activeAcademicYear' => $registration->getAcademicYear(),
                'academicYears' => $academicYears,
                'form' => $form,
                'organizations' => $organizations,
                'currentOrganization' => $this->_getOrganization(),
            )
        );
    }

    public function cancelAction()
    {
        if (!($registration = $this->_getRegistration()))
            return new ViewModel();

        $academic = $registration->getAcademic();
        $organizationStatus = $academic->getOrganizationStatus($registration->getAcademicYear());

        $statusMessage = '';

        if ($organizationStatus->getStatus()==='praesidium') {

            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'Registration could not be cancelled as the person is part of the praesidium !'
                )
            );

            $this->redirect()->toRoute(
                'secretary_admin_registration',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();

        } else if ($registration->isCancelled()) {
            $statusMessage='The registration was already succesfully cancelled !';
        } else {
            $metaData = $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
                ->findOneByAcademicAndAcademicYear($registration->getAcademic(), $registration->getAcademicYear());

            $registration->setPayed(false);

            if (null != $metaData) {
                $metaData->setBecomeMember(false);
            }

            $organizationStatus->setStatus('non_member');
            $registration->setCancelled(true);
            $this->getEntityManager()->flush();
            $statusMessage = 'The registration was successfully cancelled for '.$academic->getFirstName().' '.$academic->getLastName().'!';
        }

        if ($statusMessage != '') {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::SUCCESS,
                    'SUCCESS',
                    $statusMessage
                )
            );
        }

        $this->redirect()->toRoute(
            'secretary_admin_registration',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function searchAction()
    {
        $academicYear = $this->_getAcademicYear();
        $organization = $this->_getOrganization();

        $this->initAjax();

        switch($this->getParam('field')) {
            case 'university_identification':
                $registrations = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findAllByUniversityIdentification(
                        $this->getParam('string'),
                        $academicYear,
                        $organization
                    );
                break;
            case 'name':
                $registrations = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findAllByName(
                        $this->getParam('string'),
                        $academicYear,
                        $organization
                    );
                break;
            case 'barcode':
                $registrations = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findAllByBarcode(
                        $this->getParam('string'),
                        $academicYear,
                        $organization
                    );
                break;
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($registrations, $numResults);

        $result = array();
        foreach($registrations as $registration) {
            if ($registration->getAcademic()->canLogin()) {
                $item = (object) array();
                $item->id = $registration->getId();
                $item->universityIdentification = (
                    null !== $registration->getAcademic()->getUniversityIdentification()
                        ? $registration->getAcademic()->getUniversityIdentification()
                        : ''
                );
                $item->name = $registration->getAcademic()->getFullName();
                $item->date = $registration->getTimestamp()->format('d/m/Y H:i');
                $item->payed = $registration->hasPayed();
                $item->cancelled = $registration->isCancelled();
                $item->barcode = $registration->getAcademic()->getBarcode() ? $registration->getAcademic()->getBarcode()->getBarcode() : '';
                $item->organization = $registration->getAcademic()->getOrganization($academicYear) ? $registration->getAcademic()->getOrganization($academicYear)->getName() : '';
                $result[] = $item;
            }
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function exportAction()
    {
        $academicYear = $this->_getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        return new ViewModel(
            array(
                'activeAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
                'organizations' => $organizations,
                'currentOrganization' => $this->_getOrganization(),
            )
        );
    }

    public function downloadAction()
    {
        $academicYear = $this->_getAcademicYear();
        $organization = $this->_getOrganization();

        if ($organization) {
            $mappings = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
                ->findByAcademicYearAndOrganization($academicYear, $organization);
        } else {
            $mappings = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
                ->findByAcademicYear($academicYear);
        }

        foreach($mappings as $mapping) {
            $registration = $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Registration')
                ->findOneByAcademicAndAcademicYear($mapping->getAcademic(), $academicYear);

            if (null === $registration || $registration->hasPayed() == false)
                continue;

            $members[$mapping->getAcademic()->getId()] = array(
                'academicFirstName'               => $mapping->getAcademic()->getFirstName(),
                'academicLastName'                => $mapping->getAcademic()->getLastName(),
                'academicEmail'                   => $mapping->getAcademic()->getEmail(),
                'academicPrimaryAddressStreet'    => $mapping->getAcademic()->getPrimaryAddress() ? $mapping->getAcademic()->getPrimaryAddress()->getStreet() : '',
                'academicPrimaryAddressNumber'    => $mapping->getAcademic()->getPrimaryAddress() ? $mapping->getAcademic()->getPrimaryAddress()->getNumber() : '',
                'academicPrimaryAddressMailbox'   => $mapping->getAcademic()->getPrimaryAddress() ? $mapping->getAcademic()->getPrimaryAddress()->getMailbox() : '',
                'academicPrimaryAddressPostal'    => $mapping->getAcademic()->getPrimaryAddress() ? $mapping->getAcademic()->getPrimaryAddress()->getPostal() : '',
                'academicPrimaryAddressCity'      => $mapping->getAcademic()->getPrimaryAddress() ? $mapping->getAcademic()->getPrimaryAddress()->getCity() : '',
                'academicPrimaryAddressCountry'   => $mapping->getAcademic()->getPrimaryAddress() ? $mapping->getAcademic()->getPrimaryAddress()->getCountry() : '',
                'academicSecondaryAddressStreet'  => $mapping->getAcademic()->getSecondaryAddress() ? $mapping->getAcademic()->getSecondaryAddress()->getStreet() : '',
                'academicSecondaryAddressNumber'  => $mapping->getAcademic()->getSecondaryAddress() ? $mapping->getAcademic()->getSecondaryAddress()->getNumber() : '',
                'academicSecondaryAddressMailbox' => $mapping->getAcademic()->getSecondaryAddress() ? $mapping->getAcademic()->getSecondaryAddress()->getMailbox() : '',
                'academicSecondaryAddressPostal'  => $mapping->getAcademic()->getSecondaryAddress() ? $mapping->getAcademic()->getSecondaryAddress()->getPostal() : '',
                'academicSecondaryAddressCity'    => $mapping->getAcademic()->getSecondaryAddress() ? $mapping->getAcademic()->getSecondaryAddress()->getCity() : '',
                'academicSecondaryAddressCountry' => $mapping->getAcademic()->getSecondaryAddress() ? $mapping->getAcademic()->getSecondaryAddress()->getCountry() : '',
            );
        }

        $header = array(
            'First name',
            'Last name',
            'Email',
            'Street (Primary Address)',
            'Number (Primary Address)',
            'Mailbox (Primary Address)',
            'Postal (Primary Address)',
            'City (Primary Address)',
            'Country (Primary Address)',
            'Street (Secondary Address)',
            'Number (Secondary Address)',
            'Mailbox (Secondary Address)',
            'Postal (Secondary Address)',
            'City (Secondary Address)',
            'Country (Secondary Address)',
        );

        $exportFile = new CsvFile();
        $csvGenerator = new CsvGenerator($header, $members);
        $csvGenerator->generateDocument($exportFile);

        $this->getResponse()->getHeaders()
            ->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="members_'.$academicYear->getCode().'.csv"',
            'Content-Type' => 'text/csv',
        ));

        return new ViewModel(
            array(
                'result' => $exportFile->getContent(),
            )
        );
    }

    private function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear'))
            return $this->getCurrentAcademicYear();

        $start = AcademicYear::getDateTime($this->getParam('academicyear'));
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'secretary_admin_registration',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academicYear;
    }

    private function _getRegistration()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the registration!'
                )
            );

            $this->redirect()->toRoute(
                'secretary_admin_registration',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $registration = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Registration')
            ->findOneById($this->getParam('id'));

        if (null === $registration) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No registration with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'secretary_admin_registration',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $registration;
    }

    private function _getOrganization()
    {
        if (null === $this->getParam('organization'))
            return;

        $organization = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findOneById($this->getParam('organization'));

        return $organization;
    }
}
