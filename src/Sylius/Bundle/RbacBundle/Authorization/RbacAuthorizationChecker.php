<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Authorization;

use Sylius\Bundle\ResourceBundle\Controller\AuthorizationCheckerInterface as ResourceBundleAuthorizationCheckerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RbacAuthorizationChecker implements ResourceBundleAuthorizationCheckerInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $rbacAuthorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $rbacAuthorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $rbacAuthorizationChecker)
    {
        $this->rbacAuthorizationChecker = $rbacAuthorizationChecker;
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
