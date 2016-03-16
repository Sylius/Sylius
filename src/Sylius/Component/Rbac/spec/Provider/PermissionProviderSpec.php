<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Rbac\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Exception\PermissionNotFoundException;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Provider\PermissionProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PermissionProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $permissionRepository)
    {
        $this->beConstructedWith($permissionRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Provider\PermissionProvider');
    }

    function it_implements_Sylius_Rbac_permission_provider_interface()
    {
        $this->shouldImplement(PermissionProviderInterface::class);
    }

    function it_looks_for_permission_via_repository($permissionRepository, PermissionInterface $permission)
    {
        $permissionRepository->findOneBy(['code' => 'can_dance_on_the_table'])->shouldBeCalled()->willReturn($permission);

        $this->getPermission('can_dance_on_the_table')->shouldReturn($permission);
    }

    function it_throws_an_exception_when_permission_does_not_exist($permissionRepository)
    {
        $permissionRepository->findOneBy(['code' => 'can_dance_on_the_fridge'])->shouldBeCalled()->willReturn(null);

        $this->shouldThrow(new PermissionNotFoundException('can_dance_on_the_fridge'))->duringGetPermission('can_dance_on_the_fridge');
    }
}
