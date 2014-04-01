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

namespace CommonBundle\Component\Acl\Driver;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\Authentication\Authentication;

/**
 * A view helper that allows us to easily verify whether or not the authenticated user
 * has access to a resource. This is particularly useful for creating navigation items.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class HasAccess
{
    /**
     * @var Acl The ACL object
     */
    private $_acl = null;

    /**
     * @var Authentication The authentication object
     */
    private $_authentication = null;

    /**
     * @param Acl            $acl            The ACL object
     * @param Authentication $authentication The authentication object
     */
    public function __construct(Acl $acl, Authentication $authentication)
    {
        $this->_acl = $acl;
        $this->_authentication = $authentication;
    }

    /**
     * @param  string       $resource The resource that should be verified
     * @param  string       $action   The module that should be verified
     * @return boolean|null
     */
    public function __invoke($resource, $action)
    {
        if (null === $this->_acl)
            throw new Exception\RuntimeException('No ACL object was provided');

        if (null === $this->_authentication)
            throw new Exception\RuntimeException('No authentication object was provided');

        // Making it easier to develop new actions and controllers, without all the ACL hassle
        if ('development' == getenv('APPLICATION_ENV'))
            return true;

        if (!$this->_acl->hasResource($resource))
            return false;

        if ($this->_authentication->isAuthenticated()) {
            foreach ($this->_authentication->getPersonObject()->getRoles() as $role) {
                if (
                    $role->isAllowed(
                        $this->_acl, $resource, $action
                    )
                ) {
                    return true;
                }
            }

            return false;
        } else {
            return $this->_acl->isAllowed(
                'guest', $resource, $action
            );
        }
    }
}
