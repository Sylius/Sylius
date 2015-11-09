<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\RbacBundle\EventListener;

use Doctrine\Common\Cache\ClearableCache;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class CacheListenerSpec extends ObjectBehavior
{
    function let(ClearableCache $cache)
    {
        $this->beConstructedWith($cache);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\RbacBundle\EventListener\CacheListener');
    }

    function it_clears_the_cache_after_persisting_a_role($cache, LifecycleEventArgs $args, RoleInterface $role)
    {
        $args->getEntity()->shouldBeCalled()->willReturn($role);
        $cache->deleteAll()->shouldBeCalled();

        $this->postPersist($args);
    }

    function it_clears_the_cache_on_post_after_removing_a_role($cache, LifecycleEventArgs $args, RoleInterface $role)
    {
        $args->getEntity()->shouldBeCalled()->willReturn($role);
        $cache->deleteAll()->shouldBeCalled();

        $this->postRemove($args);
    }

    function it_clears_the_cache_after_persisting_a_permission(
        $cache,
        LifecycleEventArgs $args,
        PermissionInterface $permission
    ) {
        $args->getEntity()->shouldBeCalled()->willReturn($permission);
        $cache->deleteAll()->shouldBeCalled();

        $this->postPersist($args);
    }

    function it_clears_the_cache_on_post_after_removing_a_permission(
        $cache,
        LifecycleEventArgs $args,
        PermissionInterface $permission
    ) {
        $args->getEntity()->shouldBeCalled()->willReturn($permission);
        $cache->deleteAll()->shouldBeCalled();

        $this->postRemove($args);
    }
}
