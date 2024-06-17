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

namespace spec\Sylius\Component\Core\TokenAssigner;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;

final class UniqueIdBasedOrderTokenAssignerSpec extends ObjectBehavior
{
    private const TOKEN_LENGTH = 32;

    public function let(RandomnessGeneratorInterface $generator)
    {
        $this->beConstructedWith($generator, self::TOKEN_LENGTH);
    }

    function it_is_an_order_token_assigner(): void
    {
        $this->shouldImplement(OrderTokenAssignerInterface::class);
    }

    function it_assigns_a_token_value_for_order(RandomnessGeneratorInterface $generator, OrderInterface $order): void
    {
        $order->getTokenValue()->willReturn(null);
        $generator->generateUriSafeString(self::TOKEN_LENGTH)->willReturn('yahboiiiii');
        $order->setTokenValue('yahboiiiii')->shouldBeCalled();

        $this->assignTokenValue($order);
        $this->assignTokenValueIfNotSet($order);
    }

    function it_does_nothing_if_token_is_already_assigned(
        RandomnessGeneratorInterface $generator,
        OrderInterface $order,
    ): void {
        $order->getTokenValue()->willReturn('yahboiiiii');
        $generator->generateUriSafeString(Argument::any())->shouldNotBeCalled();
        $order->setTokenValue(Argument::any())->shouldNotBeCalled();

        $this->assignTokenValueIfNotSet($order);
    }
}
