<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\RbacBundle\Authorization;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\AuthorizationCheckerInterface as ResourceBundleAuthorizationCheckerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RbacAuthorizationCheckerSpec extends ObjectBehavior
{
    function let(AuthorizationCheckerInterface $rbacAuthorizationChecker)
    {
        $this->beConstructedWith($rbacAuthorizationChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\RbacBundle\Authorization\RbacAuthorizationChecker');
    }

    function it_implements_resource_controller_authorization_checker_interface()
    {
        $this->shouldImplement(ResourceBundleAuthorizationCheckerInterface::class);
    }

    function it_grants_access_if_permission_is_not_required(RequestConfiguration $requestConfiguration)
    {
        $requestConfiguration->hasPermission()->willReturn(false);
        $this->isGranted($requestConfiguration, 'sylius.product.foo')->shouldReturn(true);
    }

    function it_uses_rbac_authorization_checker(
        RequestConfiguration $requestConfiguration,
        AuthorizationCheckerInterface $rbacAuthorizationChecker
    ) {
        $requestConfiguration->hasPermission()->willReturn(true);
        $requestConfiguration->getPermission('sylius.product.foo')->willReturn('sylius.product.foo');
        $rbacAuthorizationChecker->isGranted('sylius.product.foo')->willReturn(false);
        $this->isGranted($requestConfiguration, 'sylius.product.foo')->shouldReturn(false);

        $rbacAuthorizationChecker->isGranted('sylius.product.foo')->willReturn(true);
        $this->isGranted($requestConfiguration, 'sylius.product.foo')->shouldReturn(true);
    }
}
