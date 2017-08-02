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

namespace spec\Sylius\Component\Core\TokenAssigner;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Sylius\Component\Core\TokenAssigner\UniqueIdBasedOrderTokenAssigner;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class UniqueIdBasedOrderTokenAssignerSpec extends ObjectBehavior
{
    public function let(RandomnessGeneratorInterface $generator)
    {
        $this->beConstructedWith($generator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UniqueIdBasedOrderTokenAssigner::class);
    }

    function it_is_an_order_token_assigner()
    {
        $this->shouldImplement(OrderTokenAssignerInterface::class);
    }

    function it_assigns_a_token_value_for_order(RandomnessGeneratorInterface $generator, OrderInterface $order)
    {
        $generator->generateUriSafeString(10)->willReturn('yahboiiiii');
        $order->setTokenValue('yahboiiiii')->shouldBeCalled();

        $this->assignTokenValue($order);
    }
}
