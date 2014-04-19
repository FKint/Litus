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

namespace StoreBundle\Controller\Admin;

use StoreBundle\Entity\Store,
    StoreBundle\Component\StoreFactory,
    StoreBundle\Form\Admin\Store\Add,
    Zend\View\Model\ViewModel;

/**
 * ArticleController
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class StoreController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function indexAction()
    {
        $stores = $this->getEntityManager()
            ->getRepository('StoreBundle\Entity\Store')
            ->findAll();

        return new ViewModel(
            array(
                'stores' => $stores,
            )
        );
    }

    public function createAction()
    {
        $form = new Add();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $name = $formData['name'];

                $storeFactory = new StoreFactory();
                $store = $storeFactory->createStore($name);

                $this->getEntityManager()->persist($store);
                $this->getEntityManager()->flush();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }
}
