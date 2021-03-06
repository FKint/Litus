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

namespace ApiBundle\Component\Controller\ActionController;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\Acl\Driver\HasAccess as HasAccessDriver,
    CommonBundle\Component\Controller\DoctrineAware,
    CommonBundle\Component\Controller\Exception\RuntimeException,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\Visit,
    Zend\Http\Header\HeaderInterface,
    Zend\Mvc\MvcEvent,
    Zend\Uri\UriFactory,
    Zend\Validator\AbstractValidator,
    Zend\View\Model\ViewModel;

/**
 * We extend the CommonBundle controller.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @method \CommonBundle\Component\Controller\Plugin\FlashMessenger flashMessenger()
 * @method \CommonBundle\Component\Controller\Plugin\HasAccess hasAccess()
 * @method \CommonBundle\Component\Controller\Plugin\Paginator paginator()
 * @method \CommonBundle\Component\Controller\Plugin\Url url()
 * @method \Zend\Http\PhpEnvironment\Response getResponse()
 * @method \Zend\Http\PhpEnvironment\Request getRequest()
 */
class ApiController extends \Zend\Mvc\Controller\AbstractActionController implements DoctrineAware
{
    /**
     * @var \CommonBundle\Component\Acl\Driver\HasAccess The access driver
     */
    private $hasAccessDriver = null;

    /**
     * @var \CommonBundle\Entity\General\Language The current language
     */
    private $language = null;

    /**
     * Execute the request.
     *
     * @param  \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->getServiceLocator()
            ->get('Zend\View\Renderer\PhpRenderer')
            ->plugin('headMeta')
            ->setCharset('utf-8');

        $this->initAuthenticationService();
        $this->initControllerPlugins();
        $this->initFallbackLanguage();
        $this->initLocalization();
        $this->initUriScheme();
        $this->initViewHelpers();

        $this->logVisit();

        if (false !== getenv('SERVED_BY')) {
            $this->getResponse()
                ->getHeaders()
                ->addHeaderLine('X-Served-By', getenv('SERVED_BY'));
        }

        $result = parent::onDispatch($e);
        $result->setTerminal(true);

        if ($this->validateKey() || $this->validateOAuth()) {
            if ('development' != getenv('APPLICATION_ENV')) {
                $this->hasAccess()->setDriver($this->hasAccessDriver);
            }

            if (
                !$this->hasAccess()->toResourceAction(
                    $this->getParam('controller'), $this->getParam('action')
                )
            ) {
                $error = $this->error(401, 'You do not have sufficient permissions to access this resource');
                $error->setOptions($result->getOptions());
                $e->setResult($error);

                return $error;
            }
        } else {
            $error = $this->error(401, 'No key or OAuth token was provided');
            $error->setOptions($result->getOptions());
            $e->setResult($error);

            return $error;
        }

        if (false !== getenv('SERVED_BY')) {
            $this->getResponse()
                ->getHeaders()
                ->addHeaderLine('X-Served-By', getenv('SERVED_BY'));
        }

        $result->flashMessenger = $this->flashMessenger();

        $e->setResult($result);

        return $result;
    }

    /**
     * Returns an error message.
     *
     * @param  integer                    $code    The HTTP status code
     * @param  string                     $message The error message
     * @return \Zend\View\Model\ViewModel
     */
    public function error($code, $message)
    {
        if (!$this->isAuthorizeAction()) {
            $this->initJson();
        }

        $this->getResponse()->setStatusCode($code);

        $error = array(
            'message' => $message,
        );

        return new ViewModel(
            array(
                'error' => (object) $error,
            )
        );
    }

    /**
     * @return null
     */
    private function logVisit()
    {
        $saveVisit = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.save_visits');

        if ($saveVisit == '1') {
            $server = $this->getRequest()->getServer();
            $route = $this->getEvent()->getRouteMatch();

            $visit = new Visit(
                $server->get('HTTP_USER_AGENT'),
                $server->get('REQUEST_URI'),
                $server->get('REQUEST_METHOD'),
                $route->getParam('controller'),
                $route->getParam('action'),
                $this->getAuthentication()->isAuthenticated() ? $this->getAuthentication()->getPersonObject() : null
            );

            $this->getEntityManager()->persist($visit);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Initializes the fallback language and sets it as the default so that it is
     * accessible troughout the application.
     *
     * @return void
     * @throws RuntimeException
     */
    private function initFallbackLanguage()
    {
        try {
            $fallbackLanguage = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('fallback_language')
                );

            if (null !== $fallbackLanguage) {
                \Locale::setDefault($fallbackLanguage->getAbbrev());
            }
        } catch (\Exception $e) {
            throw new RuntimeException('Unable to initialize fallback language.');
        }
    }

    /**
     * Initializes our custom view helpers.
     *
     * @return void
     */
    private function initViewHelpers()
    {
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');

        $renderer->plugin('url')
            ->setLanguage($this->getLanguage())
            ->setRouter($this->getServiceLocator()->get('router'));

        // HasAccess View Helper
        $renderer->plugin('hasAccess')->setDriver(
            new HasAccessDriver(
                $this->getAcl(),
                $this->getAuthentication()->isAuthenticated(),
                $this->getAuthentication()->isAuthenticated() ? $this->getAuthentication()->getPersonObject() : null
            )
        );

        // GetParam View Helper
        $renderer->plugin('getParam')->setRouteMatch(
            $this->getEvent()->getRouteMatch()
        );

        // StaticMap View Helper
        $renderer->plugin('staticMap')
            ->setEntityManager($this->getEntityManager());
    }

    /**
     * Modifies the reponse headers for a JSON reponse.
     *
     * @param  array $additionalHeaders Any additional headers that should be set
     * @return void
     */
    protected function initJson(array $additionalHeaders = array())
    {
        unset($additionalHeaders['Content-Type']);

        $headers = $this->getResponse()->getHeaders();

        $contentType = $headers->get('Content-Type');
        if ($contentType instanceof HeaderInterface) {
            $headers->removeHeader($contentType);
        }

        $headers->addHeaders(
            array_merge(
                array(
                    'Content-Type' => 'application/json',
                ),
                $additionalHeaders
            )
        );
    }

    /**
     * @return null
     */
    private function initAuthenticationService()
    {
        $this->getServiceLocator()->get('authentication_service')
            ->setRequest($this->getRequest())
            ->setResponse($this->getResponse());
    }

    /**
     * Initializes our custom controller plugins.
     *
     * @return void
     */
    private function initControllerPlugins()
    {
        // Url Plugin
        $this->url()->setLanguage($this->getLanguage());

        // HasAccess Plugin
        $this->hasAccess()->setDriver(
            new HasAccessDriver(
                $this->getAcl(),
                $this->getAuthentication()->isAuthenticated(),
                $this->getAuthentication()->isAuthenticated() ? $this->getAuthentication()->getPersonObject() : null
            )
        );
    }

    /**
     * Initializes the localization.
     *
     * @return void
     */
    private function initLocalization()
    {
        $translator = $this->getTranslator()->getTranslator();

        $translator->setCache($this->getCache())
            ->setLocale($this->getLanguage()->getAbbrev());

        AbstractValidator::setDefaultTranslator($this->getTranslator());
    }

    /**
     * Initializes custom URL schemes.
     *
     * @return void
     */
    private function initUriScheme()
    {
        UriFactory::registerScheme('litus', 'ApiBundle\Component\Uri\Litus');
    }

    /**
     * Checks if the current action is the OAuth authorize action.
     *
     * @return boolean
     */
    private function isAuthorizeAction()
    {
        return ('authorize' == $this->getParam('action') || 'shibboleth' == $this->getParam('action')) && 'api_oauth' == $this->getParam('controller');
    }

    /**
     * Checks if the current action is an OAuth action.
     *
     * @return boolean
     */
    private function isOAuthAction()
    {
        return 'api_oauth' == $this->getParam('controller');
    }

    /**
     * Returns the ACL object.
     *
     * @return \CommonBundle\Component\Acl\Acl
     */
    private function getAcl()
    {
        if (null !== $this->getCache()) {
            if (!$this->getCache()->hasItem('CommonBundle_Component_Acl_Acl')) {
                $acl = new Acl(
                    $this->getEntityManager()
                );

                $this->getCache()->setItem('CommonBundle_Component_Acl_Acl', $acl);
            }

            return $this->getCache()->getItem('CommonBundle_Component_Acl_Acl');
        }

        return new Acl(
            $this->getEntityManager()
        );
    }

    /**
     * We want an easy method to retrieve the Authentication from
     * the DI container.
     *
     * @return \CommonBundle\Component\Authentication\Authentication
     */
    public function getAuthentication()
    {
        return $this->getServiceLocator()->get('authentication');
    }

    /**
     * We want an easy method to retrieve the Authentication Service
     * from the DI container.
     *
     * @return \CommonBundle\Component\Authentication\AbstractAuthenticationService
     */
    public function getAuthenticationService()
    {
        return $this->getServiceLocator()->get('authentication_doctrineservice');
    }

    /**
     * We want an easy method to retrieve the Cache from
     * the DI container.
     *
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getCache()
    {
        return $this->getServiceLocator()->get('cache');
    }

    /**
     * Get the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getCurrentAcademicYear($organization = false)
    {
        if ($organization) {
            return AcademicYear::getOrganizationYear($this->getEntityManager());
        }

        return AcademicYear::getUniversityYear($this->getEntityManager());
    }

    /**
     * We want an easy method to retrieve the DocumentManager from
     * the DI container.
     *
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
    }

    /**
     * We want an easy method to retrieve the EntityManager from
     * the DI container.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    /**
     * Helper method that retrieves the API key.
     *
     * @param  string                $field The name of the field that contains the key
     * @return \ApiBundle\Entity\Key
     */
    protected function getKey($field = 'key')
    {
        if ($this->isOAuthAction()) {
            $field = 'client_id';
        }

        $code = $this->getRequest()->getQuery($field);
        if (null === $code && $this->getRequest()->isPost()) {
            $code = $this->getRequest()->getPost($field);
        }

        $key = $this->getEntityManager()
            ->getRepository('ApiBundle\Entity\Key')
            ->findOneActiveByCode($code);

        return $key;
    }

    /**
     * Helper method that retrieves the OAuth 2 access token.
     *
     * @param  string                           $field The name of the field that contains the access token
     * @return \ApiBundle\Document\Token\Access
     */
    protected function getAccessToken($field = 'access_token')
    {
        $code = $this->getRequest()->getQuery($field);
        if (null === $code && $this->getRequest()->isPost()) {
            $code = $this->getRequest()->getPost($field);
        }

        $accessToken = $this->getDocumentManager()
            ->getRepository('ApiBundle\Document\Token\Access')
            ->findOneActiveByCode($code);

        return $accessToken;
    }

    /**
     * Returns the language that is currently requested.
     *
     * @return \CommonBundle\Entity\General\Language
     */
    protected function getLanguage()
    {
        if ('' != $this->getRequest()->getPost('language', '')) {
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($this->getRequest()->getPost('language'));
        }

        if (null !== $this->language) {
            return $this->language;
        }

        if ($this->getParam('language')) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($this->getParam('language'));
        }

        if (!isset($language)) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('nl');

            if (null === $language) {
                $language = new Language(
                    'nl', 'Nederlands'
                );

                $this->getEntityManager()->persist($language);
                $this->getEntityManager()->flush();
            }
        }

        $this->language = $language;

        return $language;
    }

    /**
     * Gets a parameter from a GET request.
     *
     * @param  string $param   The parameter's key
     * @param  mixed  $default The default value, returned when the parameter is not found
     * @return string
     */
    public function getParam($param, $default = null)
    {
        return $this->getEvent()->getRouteMatch()->getParam($param, $default);
    }

    /**
     * Retrieve the common session storage from the DI container.
     *
     * @return \Zend\Session\Container
     */
    public function getSessionStorage()
    {
        return $this->getServiceLocator()->get('common_sessionstorage');
    }

    /**
     * We want an easy method to retrieve the Mail Transport from
     * the DI container.
     *
     * @return \Zend\Mail\Transport\TransportInterface
     */
    public function getMailTransport()
    {
        return $this->getServiceLocator()->get('mail_transport');
    }

    /**
     * We want an easy method to retrieve the Translator from
     * the DI container.
     *
     * @return \Zend\Mvc\I18n\Translator
     */
    public function getTranslator()
    {
        return $this->getServiceLocator()->get('translator');
    }

    /**
     * @return \CommonBundle\Component\Form\Factory
     */
    protected function getFormFactory()
    {
        return $this->getServiceLocator()->get('formfactory.bootstrap');
    }

    /**
     * @param  string                            $name
     * @param  array|object|null                 $data
     * @return \CommonBundle\Component\Form\Form
     */
    public function getForm($name, $data = null)
    {
        $form = $this->getFormFactory()->create(array('type' => $name), $data);

        return $form;
    }

    /**
     * Authenticates an application if an API key is provided.
     *
     * @return boolean
     */
    private function validateKey()
    {
        if ('development' == getenv('APPLICATION_ENV')) {
            return true;
        }

        if ($this->isAuthorizeAction()) {
            $this->hasAccessDriver = new HasAccessDriver(
                $this->getAcl(),
                false,
                null
            );

            return true;
        }

        $key = $this->getKey();
        if (null === $key) {
            return false;
        }

        $validateKey = $key->validate(
            $this->getRequest()->getServer('HTTP_X_FORWARDED_FOR', $this->getRequest()->getServer('REMOTE_ADDR'))
        );

        if (!$validateKey) {
            return false;
        }

        $this->hasAccessDriver = new HasAccessDriver(
            $this->getAcl(),
            true,
            $key
        );

        return true;
    }

    /**
     * Authenticates a user if an OAuth access token is provided.
     *
     * @return boolean
     */
    private function validateOAuth()
    {
        if ('development' == getenv('APPLICATION_ENV')) {
            return true;
        }

        if ($this->isAuthorizeAction()) {
            $this->hasAccessDriver = new HasAccessDriver(
                $this->getAcl(),
                false,
                null
            );

            return true;
        }

        $accessToken = $this->getAccessToken();
        if (null === $accessToken) {
            return false;
        }

        $this->hasAccessDriver = new HasAccessDriver(
            $this->getAcl(),
            true,
            $accessToken->getPerson($this->getEntityManager())
        );

        return true;
    }
}
