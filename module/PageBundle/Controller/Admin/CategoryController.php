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

namespace PageBundle\Controller\Admin;

use PageBundle\Entity\Category,
    PageBundle\Entity\Category\Translation,
    PageBundle\Form\Admin\Category\Add as AddForm,
    PageBundle\Form\Admin\Category\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * CategoryController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class CategoryController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'PageBundle\Entity\Category',
            $this->getParam('page'),
            array(
                'active' => true
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(false),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $category = new Category();

                if ('' != $formData['parent']) {
                    $parent = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Node\Page')
                        ->findOneById($formData['parent']);

                    $category->setParent($parent);
                }

                $this->getEntityManager()->persist($category);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach ($languages as $language) {
                    if ('' != $formData['name_' . $language->getAbbrev()]) {
                        $translation = new Translation(
                            $category,
                            $language,
                            $formData['name_' . $language->getAbbrev()]
                        );

                        $this->getEntityManager()->persist($translation);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The category was successfully added!'
                );

                $this->redirect()->toRoute(
                    'page_admin_category',
                    array(
                        'action' => 'manage'
                    )
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

    public function editAction()
    {
        if (!($category = $this->_getCategory()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $category);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ('' != $formData['parent']) {
                    $parent = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Node\Page')
                        ->findOneById($formData['parent']);

                    $category->setParent($parent);
                }

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach ($languages as $language) {
                    $translation = $category->getTranslation($language, false);

                    if (null !== $translation) {
                        $translation->setName($formData['name_' . $language->getAbbrev()]);
                    } else {
                        if ('' != $formData['name_' . $language->getAbbrev()]) {
                            $translation = new Translation(
                                $category,
                                $language,
                                $formData['name_' . $language->getAbbrev()]
                            );

                            $this->getEntityManager()->persist($translation);
                        }
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The category was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'page_admin_category',
                    array(
                        'action' => 'manage'
                    )
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

    public function deleteAction()
    {
        $this->initAjax();

        if (!($category = $this->_getCategory()))
            return new ViewModel();

        $category->deactivate();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                )
            )
        );
    }

    private function _getCategory()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the category!'
            );

            $this->redirect()->toRoute(
                'page_admin_category',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $category = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findOneById($this->getParam('id'));

        if (null === $category) {
            $this->flashMessenger()->error(
                'Error',
                'No category with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'page_admin_category',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $category;
    }
}
