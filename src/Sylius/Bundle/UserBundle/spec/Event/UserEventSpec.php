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

namespace spec\Sylius\Bundle\UserBundle\Event;

use PhpSpec\ObjectBehavior;
use Sylius\Component\User\Model\UserInterface;

final class UserEventSpec extends ObjectBehavior
{
    function let(UserInterface $user): void
    {
        $this->beConstructedWith($user);
    }

    function it_has_user(UserInterface $user): void
    {
        $this->getUser()->shouldReturn($user);
    }
}
