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

namespace spec\Sylius\Component\Order\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\OrderSequenceInterface;

final class OrderSequenceSpec extends ObjectBehavior
{
    function it_implements_order_sequence_interface(): void
    {
        $this->shouldImplement(OrderSequenceInterface::class);
    }

    function it_has_zero_index_after_initialized(): void
    {
        $this->getIndex()->shouldReturn(0);
    }

    function it_increments_index(): void
    {
        $this->incrementIndex();
        $this->getIndex()->shouldReturn(1);
    }
}
