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

namespace CommonBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\Users\Credential,
    CommonBundle\Entity\Users\Statuses\Organization as OrganizationStatus,
    CommonBundle\Entity\Users\Statuses\University as UniversityStatus,
    CommonBundle\Form\Account\Activate as ActivateForm,
    DateTime,
    Imagick,
    SecretaryBundle\Entity\Syllabus\StudyEnrollment,
    SecretaryBundle\Entity\Syllabus\SubjectEnrollment,
    SecretaryBundle\Form\Registration\Edit as EditForm,
    Zend\File\Transfer\Transfer as FileTransfer,
    Zend\View\Model\ViewModel;

/**
 * Handles account page.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AccountController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());

        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());

        $mappings = array();
        foreach($studies as $enrollment) {
            $mappings[] = array(
                'enrollment' => $enrollment,
                'subjects' => $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByStudyAndAcademicYear($enrollment->getStudy(), $this->getCurrentAcademicYear())
            );
        }

        $subjects = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());

        $subjectIds = array();
        foreach($subjects as $enrollment)
            $subjectIds[] = $enrollment->getSubject()->getId();

        return new ViewModel(
            array(
                'academicYear' => $this->getCurrentAcademicYear(),
                'metaData' => $metaData,
                'studies' => $mappings,
                'subjects' => $subjectIds,
            )
        );
    }

    public function editAction()
    {
        $academic = $this->getAuthentication()->getPersonObject();

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $form = new EditForm(
            $academic,
            $this->getCurrentAcademicYear(),
            $metaData,
            $this->getCache(),
            $this->getEntityManager(),
            $this->getParam('identification')
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $formData['university_identification'] = $this->getParam('identification');
            if ($metaData && $metaData->becomeMember())
                $formData['become_member'] = true;
            else
                $formData['become_member'] = isset($formData['become_member']) ? $formData['become_member'] : false;
            $form->setData($formData);

            if ($form->isValid()) {
                $academic->setFirstName($formData['first_name'])
                    ->setLastName($formData['last_name'])
                    ->setEmail($formData['primary_email'] ? $formData['personal_email'] : $formData['university_email'])
                    ->setPhoneNumber($formData['phone_number'])
                    ->setSex($formData['sex'])
                    ->setBirthday(DateTime::createFromFormat('d/m/Y H:i', $formData['birthday'] . ' 00:00'))
                    ->setPersonalEmail($formData['personal_email'])
                    ->setUniversityEmail($formData['university_email']);

                $primaryCity = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Address\City')
                    ->findOneById($formData['primary_address_address_city']);
                $primaryStreet = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Address\Street')
                    ->findOneById($formData['primary_address_address_street' . $formData['primary_address_address_city']]);

                if (null !== $academic->getPrimaryAddress()) {
                    $academic->getPrimaryAddress()
                        ->setStreet($primaryStreet->getName())
                        ->setNumber($formData['primary_address_address_number'])
                        ->setMailbox($formData['primary_address_address_mailbox'])
                        ->setPostal($primaryCity->getPostal())
                        ->setCity($primaryCity->getName())
                        ->setCountry('BE');
                } else {
                    $academic->setPrimaryAddress(
                        new Address(
                            $primaryStreet->getName(),
                            $formData['primary_address_address_number'],
                            $formData['primary_address_address_mailbox'],
                            $primaryCity->getPostal(),
                            $primaryCity->getName(),
                            'BE'
                        )
                    );
                }

                if (null !== $academic->getSecondaryAddress()) {
                    $academic->getSecondaryAddress()
                        ->setStreet($formData['secondary_address_address_street'])
                        ->setNumber($formData['secondary_address_address_number'])
                        ->setMailbox($formData['secondary_address_address_mailbox'])
                        ->setPostal($formData['secondary_address_address_postal'])
                        ->setCity($formData['secondary_address_address_city'])
                        ->setCountry($formData['secondary_address_address_country']);
                } else {
                    $academic->setSecondaryAddress(
                        new Address(
                            $formData['secondary_address_address_street'],
                            $formData['secondary_address_address_number'],
                            $formData['primary_address_address_mailbox'],
                            $formData['secondary_address_address_postal'],
                            $formData['secondary_address_address_city'],
                            $formData['secondary_address_address_country']
                        )
                    );
                }

                if ($academic->canHaveUniversityStatus($this->getCurrentAcademicYear())) {
                    $status = new UniversityStatus(
                        $academic,
                        'student',
                        $this->getCurrentAcademicYear()
                    );
                    $academic->addUniversityStatus($status);
                }

                $filePath = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path');

                $file = new FileTransfer();
                if ($file->receive()) {
                    $image = new Imagick($file->getFileName());
                    $image->cropThumbnailImage(320, 240);

                    if ($academic->getPhotoPath() != '' || $academic->getPhotoPath() !== null) {
                        $fileName = $academic->getPhotoPath();
                    } else {
                        $fileName = '';
                        do{
                            $fileName = '/' . sha1(uniqid());
                        } while (file_exists($filePath . $fileName));
                    }
                    $image->writeImage($filePath . $fileName);
                    $academic->setPhotoPath($fileName);
                }

                if (null !== $metaData) {
                    $metaData->setBecomeMember($metaData->becomeMember() ? true : $formData['become_member'])
                        ->setReceiveIrReeelAtCudi($formData['irreeel'])
                        ->setBakskeByMail($formData['bakske'])
                        ->setTshirtSize($formData['tshirt_size']);
                } else {
                    $metaData = new MetaData(
                        $academic,
                        $this->getCurrentAcademicYear(),
                        $formData['become_member'],
                        $formData['irreeel'],
                        $formData['bakske'],
                        $formData['tshirt_size']
                    );
                    $this->getEntityManager()->persist($metaData);
                }

                $academic->activate(
                    $this->getEntityManager(),
                    $this->getMailTransport()
                );

                $registration = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());
                if (null === $registration) {
                    $registration = new Registration(
                        $academic,
                        $this->getCurrentAcademicYear()
                    );
                    $this->getEntityManager()->persist($registration);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'Your data was succesfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'account'
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function studiesAction()
    {
        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllParentsByAcademicYear($this->getCurrentAcademicYear());

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());

        $studyIds = array();
        foreach($enrollments as $enrollment)
            $studyIds[] = $enrollment->getStudy()->getId();

        return new ViewModel(
            array(
                'studies' => $studies,
                'enrollments' => $studyIds,
            )
        );
    }

    public function saveStudiesAction()
    {
        $this->initAjax();

        $data = $this->getRequest()->getPost();
        $academic = $this->getAuthentication()->getPersonObject();

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        foreach($enrollments as $enrollment)
            $this->getEntityManager()->remove($enrollment);

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        foreach($enrollments as $enrollment)
            $this->getEntityManager()->remove($enrollment);

        if (!empty($data['studies'])) {
            foreach($data['studies'] as $id) {
                $study = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($id);
                $this->getEntityManager()->persist(new StudyEnrollment($academic, $this->getCurrentAcademicYear(), $study));

                $subjects = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByStudyAndAcademicYear($study, $this->getCurrentAcademicYear());

                foreach($subjects as $subject) {
                    if ($subject->isMandatory())
                        $this->getEntityManager()->persist(new SubjectEnrollment($academic, $this->getCurrentAcademicYear(), $subject->getSubject()));
                }
            }
        }
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function subjectsAction()
    {
        $academic = $this->getAuthentication()->getPersonObject();

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $mappings = array();
        foreach($enrollments as $enrollment) {
            $mappings[] = array(
                'enrollment' => $enrollment,
                'subjects' => $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByStudyAndAcademicYear($enrollment->getStudy(), $this->getCurrentAcademicYear())
            );
        }

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $subjectIds = array();
        foreach($enrollments as $enrollment)
            $subjectIds[] = $enrollment->getSubject()->getId();

        return new ViewModel(
            array(
                'mappings' => $mappings,
                'enrollments' => $subjectIds,
            )
        );
    }

    public function saveSubjectsAction()
    {
        $this->initAjax();

        $data = $this->getRequest()->getPost();
        $academic = $this->getAuthentication()->getPersonObject();

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        foreach($enrollments as $enrollment)
            $this->getEntityManager()->remove($enrollment);

        if (!empty($data['subjects'])) {
            foreach($data['subjects'] as $id) {
                $subject = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Subject')
                    ->findOneById($id);
                $this->getEntityManager()->persist(new SubjectEnrollment($academic, $this->getCurrentAcademicYear(), $subject));
            }
        }
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function activateAction()
    {
        if (!($user = $this->_getUser()))
            return new ViewModel();

        $form = new ActivateForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $user->setCode(null)
                    ->setCredential(
                        new Credential(
                            'sha512',
                            $formData['credential']
                        )
                    );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'Your account was succesfully activated!'
                    )
                );

                $this->redirect()->toRoute(
                    'index'
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    private function _getUser()
    {
        if (null === $this->getParam('code')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No code was given to identify the user!'
                )
            );

            $this->redirect()->toRoute(
                'index'
            );

            return;
        }

        $user = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Code')
            ->findOnePersonByCode($this->getParam('code'));

        if (null === $user) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'The given code is not valid!'
                )
            );

            $this->redirect()->toRoute(
                'index'
            );

            return;
        }

        return $user;
    }
}
