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
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Rbac\Model\RoleInterface;

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
}
