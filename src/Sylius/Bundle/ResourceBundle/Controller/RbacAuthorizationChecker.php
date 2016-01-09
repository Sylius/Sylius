<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationCheckerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RbacAuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * @var RbacAuthorizationCheckerInterface
     */
    private $rbacAuthorizationChecker;

    /**
     * @param RbacAuthorizationCheckerInterface $rbacAuhtorizationChecker
     */
    public function __construct(RbacAuthorizationCheckerInterface $rbacAuhtorizationChecker)
    {
        $this->rbacAuthorizationChecker = $rbacAuhtorizationChecker;
    }

    /**
     * @param RequestConfiguration $requestConfiguration
     * @param string $permission
     *
     * @return bool
     */
    public function isGranted(RequestConfiguration $requestConfiguration, $permission)
    {
        if (!$requestConfiguration->hasPermission()) {
            return true;
        }

        return $this->rbacAuthorizationChecker->isGranted($permission);
    }
}
