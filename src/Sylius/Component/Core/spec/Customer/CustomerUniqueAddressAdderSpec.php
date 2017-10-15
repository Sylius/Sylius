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

namespace spec\Sylius\Component\Core\Customer;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Core\Customer\CustomerAddressAdderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CustomerUniqueAddressAdderSpec extends ObjectBehavior
{
    function let(AddressComparatorInterface $addressComparator): void
    {
        $this->beConstructedWith($addressComparator);
    }

    function it_implements_address_adder_interface(): void
    {
        $this->shouldImplement(CustomerAddressAdderInterface::class);
    }

    function it_does_nothing_when_an_address_is_already_present_on_the_customer(
        AddressComparatorInterface $addressComparator,
        CustomerInterface $customer,
        AddressInterface $address,
        Collection $addresses,
        \Iterator $iterator
    ): void {
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true);
        $iterator->current()->willReturn($address);

        $addresses->getIterator()->willReturn($iterator);
        $customer->getAddresses()->willReturn($addresses);

        $addressComparator->equal($address, $address)->willReturn(true);

        $customer->addAddress($address)->shouldNotBeCalled();

        $this->add($customer, $address);
    }

    function it_adds_an_address_when_no_other_is_present_on_the_customer(
        AddressComparatorInterface $addressComparator,
        CustomerInterface $customer,
        AddressInterface $address,
        Collection $addresses,
        \Iterator $iterator
    ): void {
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(false);

        $addresses->getIterator()->willReturn($iterator);
        $customer->getAddresses()->willReturn($addresses);

        $addressComparator->equal(
            Argument::type(AddressInterface::class),
            Argument::type(AddressInterface::class)
        )->shouldNotBeCalled();

        $customer->addAddress($address)->shouldBeCalled();

        $this->add($customer, $address);
    }

    function it_adds_an_address_when_different_than_the_ones_present_on_the_customer(
        AddressComparatorInterface $addressComparator,
        CustomerInterface $customer,
        AddressInterface $customerAddress,
        AddressInterface $newAddress,
        Collection $addresses,
        \Iterator $iterator
    ): void {
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true);
        $iterator->current()->willReturn($customerAddress);
        $iterator->valid()->willReturn(false);

        $addresses->getIterator()->willReturn($iterator);
        $customer->getAddresses()->willReturn($addresses);

        $addressComparator->equal($customerAddress, $newAddress)->willReturn(false);

        $customer->addAddress($newAddress)->shouldBeCalled();

        $this->add($customer, $newAddress);
    }
}
