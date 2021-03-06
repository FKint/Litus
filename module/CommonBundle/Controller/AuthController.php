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

namespace CommonBundle\Controller;

use CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter,
    CommonBundle\Component\Authentication\Authentication,
    Zend\View\Model\ViewModel;

/**
 * AuthController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AuthController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function loginAction()
    {
        $form = $this->getForm('common_auth_login');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $this->getAuthentication()->forget();

                $this->getAuthentication()->authenticate(
                    $formData['username'], $formData['password'], $formData['remember_me']
                );

                if ($this->getAuthentication()->isAuthenticated()) {
                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'You have been successfully logged in!'
                    );

                    $this->redirect()->toRoute(
                        'common_index',
                        array(
                            'language' => $this->getLanguage()->getAbbrev(),
                        )
                    );
                } else {
                    $this->flashMessenger()->error(
                        'Error',
                        'You could not be logged in!'
                    );

                    $this->redirect()->toRoute(
                        'common_index',
                        array(
                            'action' => 'index',
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function logoutAction()
    {
        $session = $this->getAuthentication()->forget();

        if (null !== $session && $session->isShibboleth()) {
            $shibbolethLogoutUrl = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shibboleth_logout_url');

            $this->redirect()->toUrl($shibbolethLogoutUrl);
        } else {
            $this->redirect()->toRoute(
                'common_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );
        }

        return new ViewModel();
    }

    public function shibbolethAction()
    {
        if ((null !== $this->getParam('identification')) && (null !== $this->getParam('hash'))) {
            $authentication = new Authentication(
                new ShibbolethAdapter(
                    $this->getEntityManager(),
                    'CommonBundle\Entity\User\Person\Academic',
                    'universityIdentification'
                ),
                $this->getAuthenticationService()
            );

            $code = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
                ->findLastByUniversityIdentification($this->getParam('identification'));

            if (null !== $code) {
                if ($code->validate($this->getParam('hash'))) {
                    $this->getEntityManager()->remove($code);
                    $this->getEntityManager()->flush();

                    $this->getAuthentication()->forget();

                    $authentication->authenticate(
                        $this->getParam('identification'), '', true, true
                    );

                    if ($authentication->isAuthenticated()) {
                        $registrationEnabled = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('secretary.enable_registration');

                        if ($registrationEnabled) {
                            $academic = $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                                ->findOneByUniversityIdentification($this->getParam('identification'));

                            if (null !== $academic && (null === $academic->getOrganizationStatus($this->getCurrentAcademicYear()) || null === $academic->getUniversityStatus($this->getCurrentAcademicYear()))) {
                                $this->redirect()->toRoute(
                                    'secretary_registration'
                                );

                                return new ViewModel();
                            }
                        }

                        if (null !== $code->getRedirect()) {
                            $this->redirect()->toUrl(
                                $code->getRedirect()
                            );

                            return new ViewModel();
                        }
                    } else {
                        $this->redirect()->toRoute(
                            'secretary_registration'
                        );

                        return new ViewModel();
                    }
                } else {
                    $this->logToLilo('Code not valid (' . $this->getParam('identification') . ')', array('auth'));
                }
            } else {
                $this->logToLilo('No code specifier (' . $this->getParam('identification') . ')', array('auth'));
            }
        }

        $this->flashMessenger()->error(
            'Error',
            'Something went wrong while logging you in. Please try again later.'
        );

        $this->redirect()->toRoute(
            'common_index'
        );

        return new ViewModel();
    }
}
