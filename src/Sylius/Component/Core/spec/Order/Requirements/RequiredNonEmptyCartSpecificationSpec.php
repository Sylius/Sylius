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

namespace spec\Sylius\Component\Core\Order\Requirements;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;

class RequiredNonEmptyCartSpecificationSpec extends ObjectBehavior
{
    function it_is_not_satisfied_by_anonymous_object(): void
    {
        $this->isSatisfiedBy(new class(){})->shouldBe(false);
    }

    function it_is_satisfied_by_non_empty_order(OrderInterface $order): void
    {
        $order->isEmpty()->willReturn(false);

        $this->isSatisfiedBy($order)->shouldBe(true);
    }

    function it_is_not_satisfied_by_empty_order(OrderInterface $order): void
    {
        $order->isEmpty()->willReturn(true);

        $this->isSatisfiedBy($order)->shouldBe(false);
    }
}
