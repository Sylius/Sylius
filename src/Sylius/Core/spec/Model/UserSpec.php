<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Core\Model\UserInterface;
use Sylius\Rbac\Model\RoleInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Core\Model\User');
    }

    function it_implements_user_component_interface()
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
}
