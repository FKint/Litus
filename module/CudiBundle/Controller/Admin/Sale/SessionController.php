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

namespace CudiBundle\Controller\Admin\Sale;

use CommonBundle\Component\Util\WebSocket as WebSocketUtil,
    CommonBundle\Entity\General\Bank\CashRegister,
    CudiBundle\Entity\Sale\Session,
    Zend\View\Model\ViewModel;

/**
 * SessionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SessionController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Sale\Session',
            $this->getParam('page'),
            array(),
            array('openDate' => 'DESC')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('cudi_sale_session_add');

        $lastSession = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->getLast();

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $cashRegister = $form->hydrateObject();

                $this->getEntityManager()->persist($cashRegister);

                $session = new Session($cashRegister, $this->getAuthentication()->getPersonObject());
                $this->getEntityManager()->persist($session);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The session was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session',
                    array(
                        'action' => 'edit',
                        'id' => $session->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'lastSession' => $lastSession,
            )
        );
    }

    public function editAction()
    {
        if (!($session = $this->getSessionEntity())) {
            return new ViewModel();
        }

        $session->setEntityManager($this->getEntityManager());

        $form = $this->getForm('cudi_sale_session_comment', array(
            'session' => $session,
        ));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $session->setComment($formData['comment']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The comment was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session',
                    array(
                        'action' => 'edit',
                        'id' => $session->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
            ->findAll();

        $devices = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
            ->findAll();

        return new ViewModel(
            array(
                'session' => $session,
                'units'   => $units,
                'devices' => $devices,
                'form' => $form,
            )
        );
    }

    public function editRegisterAction()
    {
        if (!($cashRegister = $this->getCashRegisterEntity())) {
            return new ViewModel();
        }

        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneByCashRegister($cashRegister);

        $form = $this->getForm('cudi_sale_session_edit', array(
            'cash_register' => $cashRegister,
        ));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The cash register was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session',
                    array(
                        'action' => 'edit',
                        'id' => $session->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'session' => $session,
            )
        );
    }

    public function closeAction()
    {
        if (!($session = $this->getSessionEntity())) {
            return new ViewModel();
        }

        $session->setEntityManager($this->getEntityManager());

        $form = $this->getForm('cudi_sale_session_close', array(
            'cash_register' => $session->getOpenRegister(),
        ));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $cashRegister = $form->hydrateObject();
                $this->getEntityManager()->persist($cashRegister);

                $autoExpire = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.enable_automatic_expire');

                if ($autoExpire) {
                    $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Booking')
                        ->expireBookings($this->getMailTransport());
                }

                $session->close($cashRegister);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The session was successfully closed!'
                );

                $this->redirect()->toRoute(
                       'cudi_admin_sales_session',
                       array(
                           'action' => 'edit',
                           'id' => $session->getId(),
                       )
                   );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'session' => $session,
            )
        );
    }

    public function queueItemsAction()
    {
        if (!($session = $this->getSessionEntity())) {
            return new ViewModel();
        }

        $items = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findBySession($session);

        return new ViewModel(
            array(
                'session' => $session,
                'items' => $items,
            )
        );
    }

    public function killSocketAction()
    {
        $this->initAjax();

        return new ViewModel(
            array(
                'result' => WebSocketUtil::kill($this->getEntityManager(), 'cudi:sale-queue'),
            )
        );
    }

    /**
     * @return Session|null
     */
    private function getSessionEntity()
    {
        $session = $this->getEntityById('CudiBundle\Entity\Sale\Session');

        if (!($session instanceof Session)) {
            $this->flashMessenger()->error(
                'Error',
                'No session was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $session;
    }

    /**
     * @return CashRegister|null
     */
    private function getCashRegisterEntity()
    {
        $cashRegister = $this->getEntityById('CommonBundle\Entity\General\Bank\CashRegister');

        if (!($cashRegister instanceof CashRegister)) {
            $this->flashMessenger()->error(
                'Error',
                'No cash register was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $cashRegister;
    }
}
