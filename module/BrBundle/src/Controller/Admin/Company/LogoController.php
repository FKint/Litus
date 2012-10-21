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

namespace BrBundle\Controller\Admin\Company;

use BrBundle\Entity\Company\Logo,
    BrBundle\Form\Admin\Company\Logo\Add as AddForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    Imagick,
    Zend\File\Transfer\Transfer as FileTransfer,
    Zend\Validator\File\Size as SizeValidator,
    Zend\Validator\File\IsImage as ImageValidator,
    Zend\View\Model\ViewModel;

/**
 * LogoController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class LogoController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($company = $this->_getCompany()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Logo')
                ->findAllByCompany($company),
            $this->getParam('page')
        );

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'company' => $company,
                'filePath' => $filePath,
            )
        );
    }

    public function addAction()
    {
        if (!($company = $this->_getCompany()))
            return new ViewModel();

        $form = new AddForm($this->getEntityManager(), $company);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $filePath = 'public/' . $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.public_logo_path') . '/';

                $upload = new FileTransfer();
                $upload->addValidator(new SizeValidator(array('max' => '10MB')));
                $upload->addValidator(new ImageValidator());

                if ($upload->isValid()) {
                    $upload->receive();

                    $image = new Imagick($upload->getFileName());
                    $image->setImageFormat('png');
                    $image->scaleImage(1000, 100, true);

                    $original = clone $image;

                    $image->setImageColorspace(Imagick::COLORSPACE_GRAY);

                    $all = new Imagick();
                    $all->addImage($image);
                    $all->addImage($original);
                    $all->resetIterator();
                    $combined = $all->appendImages(true);
                    $combined->setImageFormat('png');

                    do{
                        $fileName = sha1(uniqid());
                    } while (file_exists($filePath . $fileName));
                    $combined->writeImage($filePath . $fileName);

                    $logo = new Logo($company, $formData['type'], $fileName, $formData['url'], $image->getImageWidth(), $image->getImageHeight());
                    $this->getEntityManager()->persist($logo);

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'The logo has successfully been added!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'admin_company_logo',
                        array(
                            'action' => 'manage',
                            'id' => $company->getId(),
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        return new ViewModel(
            array(
                'company' => $company,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($logo = $this->_getLogo()))
            return new ViewModel();

        $filePath = 'public/' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path') . '/';

        if (file_exists($filePath . $logo->getPath()))
            unlink($filePath . $logo->getPath());

        $this->getEntityManager()->remove($logo);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    private function _getCompany()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the company!'
                )
            );

            $this->redirect()->toRoute(
                'admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No company with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $company;
    }

    private function _getLogo()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the logo!'
                )
            );

            $this->redirect()->toRoute(
                'admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $logo = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Logo')
            ->findOneById($this->getParam('id'));

        if (null === $logo) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No logo with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $logo;
    }
}
