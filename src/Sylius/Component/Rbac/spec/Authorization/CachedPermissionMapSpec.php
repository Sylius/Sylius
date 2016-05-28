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

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Authorization\PermissionMapInterface;
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CachedPermissionMapSpec extends ObjectBehavior
{
    function let(PermissionMapInterface $map, Cache $cache)
    {
        $this->beConstructedWith($map, $cache);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Authorization\CachedPermissionMap');
    }

    function it_is_a_permission_map()
    {
        $this->shouldHaveType(PermissionMapInterface::class);
    }

    function it_uses_another_map_to_get_all_permissions($map, Collection $permissions, RoleInterface $role)
    {
        $map->getPermissions($role)->shouldBeCalled()->willReturn($permissions);

        $this->getPermissions($role)->shouldReturn($permissions);
    }

    function it_checks_if_permission_is_in_the_cached_array($cache, RoleInterface $role)
    {
        $role->getCode()->shouldBeCalled()->willReturn('catalog_manager');

        $cache->contains('rbac_role:catalog_manager')->shouldBeCalled()->willReturn(true);
        $cache->fetch('rbac_role:catalog_manager')->shouldBeCalled()->willReturn(['can_eat_bananas', 'can_smash_bananas']);

        $this->hasPermission($role, 'can_eat_bananas')->shouldReturn(true);
        $this->hasPermission($role, 'can_eat_oranges')->shouldReturn(false);
    }
}
