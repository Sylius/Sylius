<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;

class AdminUserSpec extends ObjectBehavior
{
    public function it_extends_a_base_user_model(): void
    {
        $this->shouldHaveType(User::class);
    }

    public function it_implements_an_admin_user_interface(): void
    {
        $this->shouldImplement(AdminUserInterface::class);
    }

    public function it_implements_a_user_interface(): void
    {
        $this->shouldImplement(UserInterface::class);
    }

    public function it_has_first_name_and_last_name(): void
    {
        $this->setFirstName('John');
        $this->getFirstName()->shouldReturn('John');

        $this->setLastName('Doe');
        $this->getLastName()->shouldReturn('Doe');
    }

    public function it_has_mutable_locale_code(): void
    {
        $this->getLocaleCode()->shouldReturn(null);
        $this->setLocaleCode('en_US');
        $this->getLocaleCode()->shouldReturn('en_US');
    }
}
