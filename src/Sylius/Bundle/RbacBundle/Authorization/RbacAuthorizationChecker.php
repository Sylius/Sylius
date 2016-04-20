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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RbacAuthorizationChecker implements ResourceBundleAuthorizationCheckerInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $auhtorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $auhtorizationChecker)
    {
        $this->authorizationChecker = $auhtorizationChecker;
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

        return $this->authorizationChecker->isGranted($permission);
    }
}
