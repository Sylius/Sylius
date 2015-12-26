<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\AuthorizationCheckerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationCheckerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RbacAuthorizationCheckerSpec extends ObjectBehavior
{
    function let(RbacAuthorizationCheckerInterface $rbacAuthorizationChecker)
    {
        $this->beConstructedWith($rbacAuthorizationChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\RbacAuthorizationChecker');
    }
    
    function it_implements_resource_controller_authorization_checker_interface()
    {
        $this->shouldImplement(AuthorizationCheckerInterface::class);
    }

    function it_grants_access_if_permission_is_set_to_false(RequestConfiguration $requestConfiguration)
    {
        $requestConfiguration->getPermission('sylius.product.foo')->willReturn(false);
        $this->isGranted($requestConfiguration, 'sylius.product.foo')->shouldReturn(true);
    }

    function it_uses_rbac_authorization_checker(
        RequestConfiguration $requestConfiguration,
        RbacAuthorizationCheckerInterface $rbacAuthorizationChecker
    )
    {
        $requestConfiguration->getPermission('sylius.product.foo')->willReturn('sylius.product.foo');
        $rbacAuthorizationChecker->isGranted('sylius.product.foo')->willReturn(false);
        $this->isGranted($requestConfiguration, 'sylius.product.foo')->shouldReturn(false);

        $rbacAuthorizationChecker->isGranted('sylius.product.foo')->willReturn(true);
        $this->isGranted($requestConfiguration, 'sylius.product.foo')->shouldReturn(true);
    }
}
