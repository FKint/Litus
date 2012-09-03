<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Articles\External,
    CudiBundle\Entity\Articles\Internal,
    CudiBundle\Entity\Articles\History,
    CudiBundle\Entity\Articles\SubjectMap,
    CudiBundle\Form\Admin\Article\Add as AddForm,
    CudiBundle\Form\Admin\Article\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article')
                ->findAll(),
            $this->getParam('page')
        );

        foreach($paginator as $item)
            $item->setEntityManager($this->getEntityManager());

        $academicYear = $this->getAcademicYear();

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());
        $academicYear = $this->getAcademicYear();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                if ($formData['internal']) {
                    $binding = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Articles\Options\Binding')
                        ->findOneById($formData['binding']);

                    $frontColor = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Articles\Options\Color')
                        ->findOneById($formData['front_color']);

                    $article = new Internal(
                        $formData['title'],
                        $formData['author'],
                        $formData['publisher'],
                        $formData['year_published'],
                        $formData['isbn'] != ''? $formData['isbn'] : null,
                        $formData['url'],
                        $formData['type'],
                        $formData['downloadable'],
                        $formData['nb_black_and_white'],
                        $formData['nb_colored'],
                        $binding,
                        $formData['official'],
                        $formData['rectoverso'],
                        $frontColor,
                        $formData['perforated'],
                        $formData['colored']
                    );
                } else {
                    $article = new External(
                        $formData['title'],
                        $formData['author'],
                        $formData['publisher'],
                        $formData['year_published'],
                        $formData['isbn'] != ''? $formData['isbn'] : null,
                        $formData['url'],
                        $formData['type'],
                        $formData['downloadable']
                       );
                }

                $this->getEntityManager()->persist($article);

                if ($formData['type'] != 'common') {
                    if ($formData['subject_id'] == '') {
                        $subject = $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Subject')
                            ->findOneByCode($formData['subject']);
                    } else {
                        $subject = $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Subject')
                            ->findOneById($formData['subject_id']);
                    }
                    $mapping = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
                        ->findOneByArticleAndSubjectAndAcademicYear($article, $subject, $academicYear);

                    if (null === $mapping) {
                        $mapping = new SubjectMap($article, $subject, $academicYear, $formData['mandatory']);
                        $this->getEntityManager()->persist($mapping);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_article',
                    array(
                        'action' => 'edit',
                        'id' => $article->getId(),
                    )
                );

                return new ViewModel(
                    array(
                        'currentAcademicYear' => $academicYear,
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function editAction()
    {
        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $article);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $history = new History($article);
                $this->getEntityManager()->persist($history);

                   $article->setTitle($formData['title'])
                    ->setAuthors($formData['author'])
                    ->setPublishers($formData['publisher'])
                    ->setYearPublished($formData['year_published'])
                    ->setISBN($formData['isbn'] != ''? $formData['isbn'] : null)
                    ->setURL($formData['url'])
                    ->setIsDownloadable($formData['downloadable'])
                    ->setType(isset($formData['type']) ? $formData['type'] : 'common');

                if ($article->isInternal()) {
                    $binding = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Articles\Options\Binding')
                        ->findOneById($formData['binding']);

                    $frontPageColor = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Articles\Options\Color')
                        ->findOneById($formData['front_color']);

                    $article->setNbBlackAndWhite($formData['nb_black_and_white'])
                        ->setNbColored($formData['nb_colored'])
                        ->setBinding($binding)
                        ->setIsOfficial($formData['official'])
                        ->setIsRectoVerso($formData['rectoverso'])
                        ->setFrontColor($frontPageColor)
                        ->setIsPerforated($formData['perforated'])
                        ->setIsColored($formData['colored']);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_article',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        $saleArticle = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findOneByArticleAndAcademicYear($article, $this->getAcademicYear());

        $comments = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comments\Mapping')
            ->findByArticle($article);

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $article,
                'saleArticle' => $saleArticle,
                'comments' => $comments,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $article->setIsHistory(true);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYear();

        switch($this->getParam('field')) {
            case 'title':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllByTitle($this->getParam('string'));
                break;
            case 'author':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllByAuthor($this->getParam('string'));
                break;
            case 'publisher':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllByPublisher($this->getParam('string'));
                break;
            case 'subject':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllBySubject($this->getParam('string'), $this->getCurrentAcademicYear());
                break;
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($articles, $numResults);

        $result = array();
        foreach($articles as $article) {
            $article->setEntityManager($this->getEntityManager());
            
            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getTitle();
            $item->author = $article->getAuthors();
            $item->publisher = $article->getPublishers();
            $item->yearPublished = $article->getYearPublished() ? $article->getYearPublished() : '';
            $item->isInternal = $article->isInternal();
            $item->saleArticle = $article->getSaleArticle($academicYear) ? $article->getSaleArticle($academicYear)->getId() : 0;
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _getArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the article!'
                )
            );

            $this->redirect()->toRoute(
                'admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No article with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $article;
    }
}
