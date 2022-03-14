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
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;

class RequiredBillingAddressSpecificationSpec extends ObjectBehavior
{
    function it_is_not_satisfied_by_anonymous_object(): void
    {
        $this->isSatisfiedBy(new class(){})->shouldBe(false);
    }

    function it_is_satisfied_by_addressed_order(OrderInterface $order, AddressInterface $address): void
    {
        $order->getBillingAddress()->willReturn($address);

        $this->isSatisfiedBy($order)->shouldBe(true);
    }

    function it_is_not_satisfied_by_order(OrderInterface $order): void
    {
        $this->isSatisfiedBy($order)->shouldBe(false);
    }
}
