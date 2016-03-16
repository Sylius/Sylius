<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Rbac\Authorization;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface;
use Sylius\Component\Rbac\Authorization\PermissionMapInterface;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Provider\CurrentIdentityProviderInterface;
use Sylius\Component\Rbac\Resolver\RolesResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AuthorizationCheckerSpec extends ObjectBehavior
{
    function let(
        CurrentIdentityProviderInterface $currentIdentityProvider,
        PermissionMapInterface $permissionMap,
        RolesResolverInterface $rolesResolver
    ) {
        $this->beConstructedWith($currentIdentityProvider, $permissionMap, $rolesResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Authorization\AuthorizationChecker');
    }

    function it_implements_Sylius_Rbac_authorization_checker_interface()
    {
        $this->shouldImplement(AuthorizationCheckerInterface::class);
    }

    function it_obtains_the_current_identity_and_returns_false_if_none_available($currentIdentityProvider)
    {
        $currentIdentityProvider->getIdentity()->shouldBeCalled()->willReturn(null);

        $this->isGranted('edit_product')->shouldReturn(false);
    }

    function it_returns_false_if_none_of_current_identity_roles_has_permission(
        $currentIdentityProvider,
        IdentityInterface $identity,
        $permissionMap,
        $rolesResolver,
        RoleInterface $role1,
        RoleInterface $role2
    ) {
        $currentIdentityProvider->getIdentity()->shouldBeCalled()->willReturn($identity);
        $rolesResolver->getRoles($identity)->shouldBeCalled()->willReturn([$role1, $role2]);

        $permissionMap->hasPermission($role1, 'can_close_store')->shouldBeCalled()->willReturn(false);
        $permissionMap->hasPermission($role2, 'can_close_store')->shouldBeCalled()->willReturn(false);

        $this->isGranted('can_close_store')->shouldReturn(false);
    }

    function it_returns_true_if_any_of_current_identity_roles_has_permission(
        $currentIdentityProvider,
        IdentityInterface $identity,
        $permissionMap,
        $rolesResolver,
        PermissionInterface $permission,
        RoleInterface $role1,
        RoleInterface $role2
    ) {
        $currentIdentityProvider->getIdentity()->shouldBeCalled()->willReturn($identity);
        $rolesResolver->getRoles($identity)->shouldBeCalled()->willReturn([$role1, $role2]);

        $permissionMap->hasPermission($role1, 'can_open_store')->shouldBeCalled()->willReturn(false);
        $permissionMap->hasPermission($role2, 'can_open_store')->shouldBeCalled()->willReturn(true);

        $this->isGranted('can_open_store')->shouldReturn(true);
    }
}
