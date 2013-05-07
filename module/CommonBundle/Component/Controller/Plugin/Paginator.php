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

namespace CommonBundle\Component\Controller\Plugin;

use Doctrine\ORM\EntityManager,
    Zend\Mvc\Exception,
    Zend\Paginator\Paginator as ZendPaginator,
    Zend\Paginator\Adapter\ArrayAdapter,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * A controller plugin containing some utility methods for pagination.
 *
 * @autor Pieter Maene <pieter.maene@litus.cc>
 */
class Paginator extends \Zend\Mvc\Controller\Plugin\AbstractPlugin
{
    /**
     * @var \Zend\Paginator\Paginator $paginator The paginator
     */
    private $_paginator = null;

    /**
     * @var int The number of items on each page
     */
    private $_itemsPerPage = 25;

    /**
     * @var ServiceLocatorInterface
     */
    protected $locator;

    /**
     * Setting the number of items per page, defaults to 25.
     *
     * @param int $itemsPerPage The number of items per page
     * @return void
     */
    public function setItemsPerPage($itemsPerPage)
    {
        if (!is_int($itemsPerPage) || $itemsPerPage < 0)
            throw new Exception\InvalidArgumentException('The number of items per page has to be positive integer');

        $this->_itemsPerPage = $itemsPerPage;
    }

    /**
     * Get the number of items per page.
     *
     * @return integer
     */
    public function getItemsPerPage()
    {
        return $this->_itemsPerPage;
    }

    /**
     * Create a paginator from a given array.
     *
     * @param array $records The array containing the paginated records
     * @param int $currentPage The page we now are on
     * @param int $itemsPerPage The number of items on each page
     * @return \Zend\Paginator\Paginator
     */
    public function createFromArray(array $records, $currentPage)
    {
        $this->_paginator = new ZendPaginator(
            new ArrayAdapter($records)
        );

        $this->_paginator->setCurrentPageNumber($currentPage);
        $this->_paginator->setItemCountPerPage(
            $this->_itemsPerPage
        );

        return $this->_paginator;
    }

    /**
     * Create a paginator for a given document.
     *
     * @param string $document The name of the document that should be paginated
     * @param int $currentPage The page we now are on
     * @param array $conditions These conditions will be passed to the Repository call
     * @param array $oderBy An array containing constraints on how to order the results
     * @param int $itemsPerPage The number of items on each page
     * @return \Zend\Paginator\Paginator
     */
    public function createFromDocument($document, $currentPage, array $conditions = array(), array $orderBy = null)
    {
        return $this->createFromArray(
            (0 == count($conditions)) ?
                $this->getLocator()->get('doctrine.documentmanager.odm_default')->getRepository($document)->findBy(array(), $orderBy)->toArray() :
                $this->getLocator()->get('doctrine.documentmanager.odm_default')->getRepository($document)->findBy($conditions, $orderBy)->toArray(),
            $currentPage
        );
    }

    /**
     * Create a paginator for a given entity.
     *
     * @param string $entity The name of the entity that should be paginated
     * @param int $currentPage The page we now are on
     * @param array $conditions These conditions will be passed to the Repository call
     * @param array $oderBy An array containing constraints on how to order the results
     * @param int $itemsPerPage The number of items on each page
     * @return \Zend\Paginator\Paginator
     */
    public function createFromEntity($entity, $currentPage, array $conditions = array(), array $orderBy = null)
    {
        return $this->createFromArray(
            (0 == count($conditions)) ?
                $this->getLocator()->get('doctrine.entitymanager.orm_default')->getRepository($entity)->findBy(array(), $orderBy) :
                $this->getLocator()->get('doctrine.entitymanager.orm_default')->getRepository($entity)->findBy($conditions, $orderBy),
            $currentPage
        );
    }

    /**
     * Create a paginator for a given entity.
     *
     * @param string $entity The name of the entity that should be paginated
     * @param int $currentPage The page we now are on
     * @param array $conditions These conditions will be passed to the Repository call
     * @param array $oderBy An array containing constraints on how to order the results
     * @param int $itemsPerPage The number of items on each page
     * @return \Zend\Paginator\Paginator
     */
    public function createFromPaginatorRepository(array $records, $currentPage, $totalNumber)
    {
        $currentPage = $currentPage == 0 ? $currentPage = 1 : $currentPage;

        $prefix = array();
        if ($currentPage > 1)
            $prefix = array_fill(0, $this->_itemsPerPage * ($currentPage - 1), true);

        $suffix = array();
        if ($totalNumber - ($this->_itemsPerPage * ($currentPage)) > 0)
            $suffix = array_fill(0, $totalNumber - ($this->_itemsPerPage * ($currentPage)), true);

        $data = array_merge(
            $prefix,
            $records,
            $suffix
        );

        $this->_paginator = new ZendPaginator(
            new ArrayAdapter($data)
        );

        $this->_paginator->setCurrentPageNumber($currentPage);
        $this->_paginator->setItemCountPerPage(
            $this->_itemsPerPage
        );

        return $this->_paginator;
    }

    /**
     * A method to quickly create the array needed to build the pagination control.
     *
     * @param \Zend\Paginator\Paginator $paginator The paginator
     * @param bool $fullWidth Whether the paginationControl should be displayed using the full width or not
     * @return array
     */
    public function createControl($fullWidth = false)
    {
        $params = $this->getController()->getEvent()->getRouteMatch()->getParams();
        foreach($params as $key => $param) {
            if ('' === $param)
                unset($params[$key]);

            if (isset($params['page']))
                unset($params['page']);
        }
        return array(
            'fullWidth' => $fullWidth,
            'matchedRouteName' => $this->getController()->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'matchedRouteParams' => $params,
            'pages' => $this->_paginator->getPages(),
        );
    }

    /**
     * Get the locator
     *
     * @return ServiceLocatorInterface
     * @throws Exception\DomainException if unable to find locator
     */
    protected function getLocator()
    {
        if ($this->locator) {
            return $this->locator;
        }

        $controller = $this->getController();

        if (!$controller instanceof ServiceLocatorAwareInterface) {
            throw new Exception\DomainException('Paginator plugin requires controller implements ServiceLocatorAwareInterface');
        }
        $locator = $controller->getServiceLocator();
        if (!$locator instanceof ServiceLocatorInterface) {
            throw new Exception\DomainException('Paginator plugin requires controller composes Locator');
        }
        $this->locator = $locator;
        return $this->locator;
    }
}