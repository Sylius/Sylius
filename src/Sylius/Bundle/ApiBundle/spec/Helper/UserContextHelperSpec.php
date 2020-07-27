<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Helper\UserContextHelperInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserContextHelperSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_is_api_access_checker(): void
    {
        $this->shouldImplement(UserContextHelperInterface::class);
    }

    function it_returns_true_if_admin_user_has_admin_api_role_access(
        UserContextInterface $userContext,
        AdminUserInterface $user
    ): void {
        $userContext->getUser()->willReturn($user);

        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $this->hasAdminRoleApiAccess()->shouldReturn(true);
    }

    function it_returns_false_if_admin_user_has_not_admin_api_role_access(
        UserContextInterface $userContext,
        AdminUserInterface $user
    ): void {
        $userContext->getUser()->willReturn($user);

        $user->getRoles()->willReturn([]);

        $this->hasAdminRoleApiAccess()->shouldReturn(false);
    }

    function it_returns_true_if_shop_user_has_shop_user_api_role_access(
        UserContextInterface $userContext,
        ShopUserInterface $user
    ): void {
        $userContext->getUser()->willReturn($user);

        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $this->hasShopUserRoleApiAccess()->shouldReturn(true);
    }

    function it_returns_false_if_shop_user_has_not_shop_user_api_role_access(
        UserContextInterface $userContext,
        ShopUserInterface $user
    ): void {
        $userContext->getUser()->willReturn($user);

        $user->getRoles()->willReturn([]);

        $this->hasShopUserRoleApiAccess()->shouldReturn(false);
    }

    function it_returns_true_if_is_visitor(UserContextInterface $userContext): void
    {
        $userContext->getUser()->willReturn(null);

        $this->isVisitor()->shouldReturn(true);
    }

    function it_returns_false_if_is_not_visitor(
        UserContextInterface $userContext,
        UserInterface $user
    ): void {
        $userContext->getUser()->willReturn($user);

        $this->isVisitor()->shouldReturn(false);
    }

    function it_returns_user(
        UserContextInterface $userContext,
        UserInterface $user
    ): void {
        $userContext->getUser()->willReturn($user);

        $this->getUser()->shouldReturn($user);
    }

    function it_returns_null_if_user_does_not_exist(UserContextInterface $userContext): void
    {
        $userContext->getUser()->willReturn(null);

        $this->getUser()->shouldReturn(null);
    }
}
