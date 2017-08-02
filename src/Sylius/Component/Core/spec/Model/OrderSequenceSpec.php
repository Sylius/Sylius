<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderSequence;
use Sylius\Component\Core\Model\OrderSequenceInterface;
use Sylius\Component\Order\Model\OrderSequence as BaseOrderSequence;
use Sylius\Component\Resource\Model\VersionedInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderSequenceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrderSequence::class);
    }

    function it_implements_an_order_sequence_interface()
    {
        $this->shouldImplement(OrderSequenceInterface::class);
    }

    function it_implements_a_versioned_interface()
    {
        $this->shouldImplement(VersionedInterface::class);
    }

    function it_extends_an_order_sequence()
    {
        $this->shouldHaveType(BaseOrderSequence::class);
    }

    function it_has_version_1_by_default()
    {
        $this->getVersion()->shouldReturn(1);
    }
}
