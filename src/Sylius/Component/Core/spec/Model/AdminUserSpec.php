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

    function it_implements_user_interface()
    {
        $this->shouldImplement(UserInterface::class);
    }

    function it_extends_base_user_model()
    {
        $this->shouldHaveType(User::class);
    }
}
