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
use Sylius\Component\Core\Model\IdentifiableUserInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ShopUserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ShopUser::class);
    }

    function it_implements_user_component_interface()
    {
        $this->shouldImplement(ShopUserInterface::class);
    }

    function it_extends_base_user_model()
    {
        $this->shouldHaveType(User::class);
    }

    function it_implements_user_interface()
    {
        $this->shouldImplement(UserInterface::class);
    }

    function it_implements_identifiable_user_interface()
    {
        $this->shouldImplement(IdentifiableUserInterface::class);
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
}
