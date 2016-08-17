<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\EventListener\AddAdministratorRoleListener;
use Sylius\Bundle\RbacBundle\Doctrine\ORM\RoleRepository;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @mixin AddAdministratorRoleListener
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class AddAdministratorRoleListenerSpec extends ObjectBehavior
{
    function let(RoleRepository $roleRepository)
    {
        $this->beConstructedWith($roleRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AddAdministratorRoleListener::class);
    }

    function it_adds_administration_role(
        RoleRepository $roleRepository,
        RoleInterface $role,
        AdminUserInterface $user,
        GenericEvent $event
    ) {
        $event->getSubject()->willReturn($user);
        $roleRepository->findOneBy(['code' => 'administrator'])->willReturn($role);
        $user->addAuthorizationRole($role)->shouldBeCalled();

        $this->addAdministrationRole($event);
    }

    function it_throws_runtime_exception_if_subject_is_not_admin_user(
        RoleRepository $roleRepository,
        RoleInterface $role,
        CustomerInterface $customer,
        GenericEvent $event
    ) {
        $event->getSubject()->willReturn($customer);
        $roleRepository->findOneBy(['code' => 'administrator'])->willReturn($role);

        $this->shouldThrow(\RuntimeException::class)->during('addAdministrationRole', [$event]);
    }

    function it_throws_runtime_exception_if_there_is_no_administration_role_available(
        RoleRepository $roleRepository,
        AdminUserInterface $user,
        GenericEvent $event
    ) {
        $event->getSubject()->willReturn($user);
        $roleRepository->findOneBy(['code' => 'administrator'])->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)->during('addAdministrationRole', [$event]);
    }
}
