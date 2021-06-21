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

namespace spec\Sylius\Component\Core\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Resolver\TaxationAddressResolverInterface;

final class TaxationAddressResolverSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(false, true);
    }

    public function it_implements_taxation_address_resolver_interface(): void
    {
        $this->shouldImplement(TaxationAddressResolverInterface::class);
    }

    public function it_should_return_billing_address_from_order_if_it_has_default_parameter(
        OrderInterface $order,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress
    ): void {
        $this->beConstructedWith(false);
        $order->setBillingAddress($billingAddress);
        $order->setShippingAddress($shippingAddress);

        $order->getBillingAddress()->willReturn($billingAddress);

        $this->getTaxationAddressFromOrder($order)->shouldReturn($billingAddress);
        $this->getTaxationAddressFromOrder($order)->shouldNotReturn($shippingAddress);
    }

    public function it_should_return_shipping_address_from_order_if_parameter_is_true(
        OrderInterface $order,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress
    ): void {
        $this->beConstructedWith(true);
        $order->setBillingAddress($billingAddress);
        $order->setShippingAddress($shippingAddress);

        $order->getShippingAddress()->willReturn($shippingAddress);

        $this->getTaxationAddressFromOrder($order)->shouldReturn($shippingAddress);
        $this->getTaxationAddressFromOrder($order)->shouldNotReturn($billingAddress);
    }

}
