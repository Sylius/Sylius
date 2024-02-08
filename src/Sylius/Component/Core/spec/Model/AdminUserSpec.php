<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;

final class AdminUserSpec extends ObjectBehavior
{
    function it_extends_a_base_user_model(): void
    {
        $this->shouldHaveType(User::class);
    }

    function it_implements_an_admin_user_interface(): void
    {
        $this->shouldImplement(AdminUserInterface::class);
    }

    function it_implements_a_user_interface(): void
    {
        $this->shouldImplement(UserInterface::class);
    }

    function it_has_first_name_and_last_name(): void
    {
        $this->setFirstName('John');
        $this->getFirstName()->shouldReturn('John');

        $this->setLastName('Doe');
        $this->getLastName()->shouldReturn('Doe');
    }

    function it_has_mutable_locale_code(): void
    {
        $this->getLocaleCode()->shouldReturn(null);
        $this->setLocaleCode('en_US');
        $this->getLocaleCode()->shouldReturn('en_US');
    }

    function it_does_not_have_an_avatar_by_default(): void
    {
        $this->getAvatar()->shouldReturn(null);
    }

    function its_image_is_mutable(ImageInterface $image): void
    {
        $this->setImage($image);
        $this->getImage()->shouldReturn($image);
    }

    function its_avatar_is_an_image(ImageInterface $image): void
    {
        $this->setAvatar($image);
        $this->getAvatar()->shouldReturn($image);
    }
}
