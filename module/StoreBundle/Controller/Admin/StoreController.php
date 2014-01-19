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

use StoreBundle\Entity\Store,
    StoreBundle\Factory\StoreFactory,
    StoreBundle\Form\Admin\Store\Add,
    Zend\View\Model\ViewModel;

/**
 * ArticleController
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class StoreController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $stores = $this->getEntityManager()
                    ->getRepository('StoreBundle\Entity\Store')
                    ->findAllQuery();

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
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }
}
