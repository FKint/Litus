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

namespace MailBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    MailBundle\Form\Admin\Study\Mail as MailForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Mail\Message,
    Zend\Mime\Part,
    Zend\Mime\Mime,
    Zend\Mime\Message as MimeMessage,
    Zend\Validator\File\Size as SizeValidator,
    Zend\View\Model\ViewModel;


/**
 * StudyController
 *
 * @autor Niels Avonds <niels.avonds@litus.cc>>
 */
class StudyController extends \CommonBundle\Component\Controller\ActionController\AdminController
{

    public function sendAction()
    {
        $currentYear = $this->getCurrentAcademicYear();

        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllParentsByAcademicYear($currentYear);

        $form = new MailForm($studies);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {

                $upload = new FileUpload();

                $upload->addValidator(new SizeValidator(array('max' => '50MB')));

                if ($upload->isValid()) {

                    $enrollments = array();

                    $studyIds = $formData['studies'];

                    foreach ($studyIds as $studyId) {

                        $study = $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Study')
                            ->findOneById($studyId);

                        $children = $study->getAllChildren();

                        foreach ($children as $child) {
                            $enrollments = array_merge($enrollments, $this->getEntityManager()
                                ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                                ->findAllByStudyAndAcademicYear($child, $currentYear));
                        }

                        $enrollments = array_merge($enrollments, $this->getEntityManager()
                            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                            ->findAllByStudyAndAcademicYear($study, $currentYear));
                    }

                    $mailAddress = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('system_mail_address');

                    $mailName = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('system_mail_name');

                    $body = $formData['message'];

                    $bccs = preg_split("/[,;\s]+/", $formData['bcc']);

                    if ($formData['test']) {
                        $body = $body . '\n\n==\n\n This mail would have been sent to:\n';
                        foreach($enrollments as $enrollment)
                            $body = $body . $enrollment->getAcademic()->getEmail() . '\n';

                        foreach($bccs as $bcc)
                            $body = $body . $bcc . '\n';

                        $mailAddress = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('system_administrator_mail');

                        $mailName = 'IT Administrator';
                    }

                    $part = new Part($body);
                    if ($formData['html'])
                        $part->type = Mime::TYPE_HTML;
                    else
                        $part->type = Mime::TYPE_TEXT;
                    $message = new MimeMessage();
                    $message->addPart($part);

                    $upload->receive();

                    foreach ($upload->getFileInfo() as $file) {
                        $part = new Part(fopen($file['tmp_name'], 'r'));
                        $part->type = $file['type'];
                        $message->addPart($part);
                    }

                    $mail = new Message();
                    $mail->setBody($message)
                        ->setFrom($mailAddress, $mailName)
                        ->setSubject($formData['subject']);

                    $mail->addTo($mailAddress, $mailName);

                    if (!$formData['test']) {
                        foreach($enrollments as $enrollment)
                            $mail->addBcc($enrollment->getAcademic()->getEmail(), $enrollment->getAcademic()->getFullName());

                        foreach($bccs as $bcc)
                            $mail->addBcc($bcc);
                    }

                    if ('development' != getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($mail);

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'The mail was successfully sent!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'admin_mail_study',
                        array(
                            'action' => 'send'
                        )
                    );

                    return new ViewModel();
                } else {
                    $dataError = $upload->getMessages();
                    $error = array();

                    foreach($dataError as $key=>$row)
                        $error[] = $row;

                    $form->setMessages(array('file'=>$error ));
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }
}
