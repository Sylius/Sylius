<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Customer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Customer\AddressAdderInterface;
use Sylius\Component\Core\Customer\CustomerOrderAddressesSaver;
use Sylius\Component\Core\Customer\OrderAddressesSaverInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CustomerOrderAddressesSaverSpec extends ObjectBehavior
{
    function let(AddressAdderInterface $addressAdder)
    {
        $this->beConstructedWith($addressAdder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerOrderAddressesSaver::class);
    }

    function it_implements_order_addresses_saver_interface()
    {
        $this->shouldImplement(OrderAddressesSaverInterface::class);
    }

    function it_saves_addresses_from_given_order(
        AddressAdderInterface $addressAdder,
        OrderInterface $order,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress
    ) {
        $order->getShippingAddress()->willReturn($shippingAddress);
        $order->getBillingAddress()->willReturn($billingAddress);

        $addressAdder->add(clone $shippingAddress)->shouldBeCalled();
        $addressAdder->add(clone $billingAddress)->shouldBeCalled();

        $this->saveAddresses($order);
    }
}
