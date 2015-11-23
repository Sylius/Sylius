<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Rbac\Authorization\Voter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Authorization\PermissionMapInterface;
use Sylius\Component\Rbac\Provider\PermissionProviderInterface;
use Sylius\Component\Rbac\Resolver\RolesResolverInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Model\PermissionInterface;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class RbacVoterSpec extends ObjectBehavior
{
    function let(
        PermissionProviderInterface $permissionProvider,
        PermissionMapInterface $permissionMap,
        RolesResolverInterface $rolesResolver
    ) {
        $this->beConstructedWith($permissionProvider, $permissionMap, $rolesResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Authorization\Voter\RbacVoter');
    }

    function it_implements_voter_interface()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Authorization\Voter\RbacVoterInterface');
    }

    function it_denies_access_if_none_of_current_identity_roles_has_permission(
        IdentityInterface $identity,
        $permissionProvider,
        $permissionMap,
        $rolesResolver,
        PermissionInterface $permission,
        RoleInterface $role1,
        RoleInterface $role2
    ) {
        $rolesResolver->getRoles($identity)->shouldBeCalled()->willReturn(array($role1, $role2));

        $role1->getCode()->shouldBeCalled()->willReturn('role1');
        $role2->getCode()->shouldBeCalled()->willReturn('role2');

        $permissionProvider->getPermission('can_close_store')->shouldBeCalled()->willReturn($permission);

        $permissionMap->hasPermission($role1, 'can_close_store')->shouldBeCalled()->willReturn(false);
        $permissionMap->hasPermission($role2, 'can_close_store')->shouldBeCalled()->willReturn(false);

        $this->isGranted($identity, 'can_close_store', null)->shouldReturn(false);
    }

    function it_grants_access_if_any_of_current_identity_roles_has_permission(
        IdentityInterface $identity,
        $permissionProvider,
        $permissionMap,
        $rolesResolver,
        PermissionInterface $permission,
        RoleInterface $role1,
        RoleInterface $role2
    ) {
        $rolesResolver->getRoles($identity)->shouldBeCalled()->willReturn(array($role1, $role2));

        $role1->getCode()->shouldBeCalled()->willReturn('role1');
        $role2->getCode()->shouldBeCalled()->willReturn('role2');

        $permissionProvider->getPermission('can_open_store')->shouldBeCalled()->willReturn($permission);

        $permissionMap->hasPermission($role1, 'can_open_store')->shouldBeCalled()->willReturn(false);
        $permissionMap->hasPermission($role2, 'can_open_store')->shouldBeCalled()->willReturn(true);

        $this->isGranted($identity, 'can_open_store', null)->shouldReturn(true);
    }
}