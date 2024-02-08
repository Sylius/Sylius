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

namespace spec\Sylius\Bundle\UserBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Model\UserInterface;

final class UserWithEncoderFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedUserFactory)
    {
        $this->beConstructedWith($decoratedUserFactory, 'encodername');
    }

    function it_is_a_factory(): void
    {
        $this->shouldHaveType(FactoryInterface::class);
    }

    function it_sets_the_given_encoder_name_on_created_user(FactoryInterface $decoratedUserFactory, UserInterface $user): void
    {
        $decoratedUserFactory->createNew()->willReturn($user);

        $user->setEncoderName('encodername')->shouldBeCalled();

        $this->createNew()->shouldReturn($user);
    }
}
