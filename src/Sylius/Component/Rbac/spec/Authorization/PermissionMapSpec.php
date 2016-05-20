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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Authorization\PermissionMapInterface;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Provider\PermissionProviderInterface;
use Sylius\Component\Rbac\Resolver\PermissionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PermissionMapSpec extends ObjectBehavior
{
    function let(PermissionProviderInterface $permissionProvider, PermissionsResolverInterface $permissionResolver)
    {
        $this->beConstructedWith($permissionProvider, $permissionResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Authorization\PermissionMap');
    }

    function it_is_a_permission_map()
    {
        $this->shouldHaveType(PermissionMapInterface::class);
    }

    function it_uses_permissions_resolver_to_obtain_whole_tree_of_permissions(
        $permissionResolver,
        RoleInterface $role,
        PermissionInterface $permission1,
        PermissionInterface $permission2
    ) {
        $permissionResolver->getPermissions($role)->shouldBeCalled()->willReturn([$permission1, $permission2]);

        $this->getPermissions($role)->shouldReturn([$permission1, $permission2]);
    }

    function it_checks_if_role_has_permission_with_given_code(
        $permissionProvider,
        $permissionResolver,
        RoleInterface $role,
        PermissionInterface $permission,
        Collection $validPermissions,
        Collection $invalidPermissions
    ) {
        $validPermissions->contains($permission)->shouldBeCalled()->willReturn(true);
        $invalidPermissions->contains($permission)->shouldBeCalled()->willReturn(false);

        $permissionProvider->getPermission('can_eat_bananas')->shouldBeCalled()->willReturn($permission);

        $permissionResolver->getPermissions($role)->shouldBeCalled()->willReturn($validPermissions);
        $this->hasPermission($role, 'can_eat_bananas')->shouldReturn(true);

        $permissionResolver->getPermissions($role)->shouldBeCalled()->willReturn($invalidPermissions);
        $this->hasPermission($role, 'can_eat_bananas')->shouldReturn(false);
    }
}
