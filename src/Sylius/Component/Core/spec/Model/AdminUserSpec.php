<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;

/**
 * @mixin AdminUser
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class AdminUserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AdminUser::class);
    }

    function it_extends_base_user_model()
    {
        $this->shouldHaveType(User::class);
    }

    function it_implements_admin_user_interface()
    {
        $this->shouldImplement(AdminUserInterface::class);
    }

    function it_implements_user_interface()
    {
        $this->shouldImplement(UserInterface::class);
    }

    function its_authorization_roles_are_mutable(RoleInterface $role)
    {
        $this->addAuthorizationRole($role);
        $this->hasAuthorizationRole($role)->shouldReturn(true);
    }

    function its_authorization_role_can_be_removed(RoleInterface $role)
    {
        $this->addAuthorizationRole($role);
        $this->removeAuthorizationRole($role);
        $this->hasAuthorizationRole($role)->shouldReturn(false);
    }

    function it_has_first_name_and_last_name()
    {
        $this->setFirstName('John');
        $this->getFirstName()->shouldReturn('John');

        $this->setLastName('Doe');
        $this->getLastName()->shouldReturn('Doe');
    }
}
