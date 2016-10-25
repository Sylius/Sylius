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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Core\Customer\AddressAdderInterface;
use Sylius\Component\Core\Customer\CustomerUniqueAddressAdder;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CustomerUniqueAddressAdderSpec extends ObjectBehavior
{
    function let(AddressComparatorInterface $addressComparator, CustomerContextInterface $customerContext)
    {
        $this->beConstructedWith($addressComparator, $customerContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerUniqueAddressAdder::class);
    }

    function it_implements_address_adder_interface()
    {
        $this->shouldImplement(AddressAdderInterface::class);
    }

    function it_does_nothing_when_there_is_no_customer(
        AddressComparatorInterface $addressComparator,
        CustomerContextInterface $customerContext,
        CustomerInterface $customer,
        AddressInterface $address
    ) {
        $customerContext->getCustomer()->willReturn(null);

        $addressComparator->same(
            Argument::type(AddressInterface::class),
            Argument::type(AddressInterface::class)
        )->shouldNotBeCalled();

        $customer->addAddress($address)->shouldNotBeCalled();

        $this->add($address);
    }

    function it_does_nothing_when_an_address_is_already_present_on_the_customer(
        AddressComparatorInterface $addressComparator,
        CustomerContextInterface $customerContext,
        CustomerInterface $customer,
        AddressInterface $address,
        Collection $addresses,
        \Iterator $iterator
    ) {
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true);
        $iterator->current()->willReturn($address);

        $addresses->getIterator()->willReturn($iterator);
        $customer->getAddresses()->willReturn($addresses);

        $customerContext->getCustomer()->willReturn($customer);

        $addressComparator->same($address, $address)->willReturn(true);

        $customer->addAddress($address)->shouldNotBeCalled();

        $this->add($address);
    }

    function it_adds_an_address_when_no_other_is_present_on_the_customer(
        AddressComparatorInterface $addressComparator,
        CustomerContextInterface $customerContext,
        CustomerInterface $customer,
        AddressInterface $address,
        Collection $addresses,
        \Iterator $iterator
    ) {
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(false);

        $addresses->getIterator()->willReturn($iterator);
        $customer->getAddresses()->willReturn($addresses);

        $customerContext->getCustomer()->willReturn($customer);

        $addressComparator->same(
            Argument::type(AddressInterface::class),
            Argument::type(AddressInterface::class)
        )->shouldNotBeCalled();

        $customer->addAddress($address)->shouldBeCalled();

        $this->add($address);
    }

    function it_adds_an_address_when_different_than_the_ones_present_on_the_customer(
        AddressComparatorInterface $addressComparator,
        CustomerContextInterface $customerContext,
        CustomerInterface $customer,
        AddressInterface $customerAddress,
        AddressInterface $newAddress,
        Collection $addresses,
        \Iterator $iterator
    ) {
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true);
        $iterator->current()->willReturn($customerAddress);
        $iterator->valid()->willReturn(false);

        $addresses->getIterator()->willReturn($iterator);
        $customer->getAddresses()->willReturn($addresses);

        $customerContext->getCustomer()->willReturn($customer);

        $addressComparator->same($customerAddress, $newAddress)->willReturn(false);

        $customer->addAddress($newAddress)->shouldBeCalled();

        $this->add($newAddress);
    }
}
