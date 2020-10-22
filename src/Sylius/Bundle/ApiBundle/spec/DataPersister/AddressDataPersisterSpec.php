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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class AddressDataPersisterSpec extends ObjectBehavior
{
    function let(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        CustomerContextInterface $customerContext
    ): void {
        $this->beConstructedWith($decoratedDataPersister, $customerContext);
    }

    function it_supports_only_address_entity(AddressInterface $address, ResourceInterface $resource): void
    {
        $this->supports($address)->shouldReturn(true);
        $this->supports($resource)->shouldReturn(false);
    }

    function it_sets_a_customer_and_marks_an_address_as_default_during_persisting_an_address(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        CustomerContextInterface $customerContext,
        CustomerInterface $customer,
        AddressInterface $address
    ): void {
        $decoratedDataPersister->persist($address, [])->shouldBeCalled();

        $customerContext->getCustomer()->willReturn($customer);
        $customer->getDefaultAddress()->willReturn(null);

        $address->setCustomer($customer)->shouldBeCalled();
        $customer->setDefaultAddress($address)->shouldBeCalled();

        $this->persist($address);
    }

    function it_sets_a_customer_without_marking_an_address_as_default_during_persisting_an_address(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        CustomerContextInterface $customerContext,
        CustomerInterface $customer,
        AddressInterface $address,
        AddressInterface $defaultAddress
    ): void {
        $decoratedDataPersister->persist($address, [])->shouldBeCalled();

        $customerContext->getCustomer()->willReturn($customer);
        $customer->getDefaultAddress()->willReturn($defaultAddress);

        $address->setCustomer($customer)->shouldBeCalled();
        $customer->setDefaultAddress($address)->shouldNotBeCalled();

        $this->persist($address);
    }

    function it_does_not_set_a_customer_if_there_is_not_logged_in_customer(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        CustomerContextInterface $customerContext,
        CustomerInterface $customer,
        AddressInterface $address
    ): void {
        $decoratedDataPersister->persist($address, [])->shouldBeCalled();

        $customerContext->getCustomer()->willReturn(null);

        $address->setCustomer($customer)->shouldNotBeCalled();
        $customer->setDefaultAddress($address)->shouldNotBeCalled();

        $this->persist($address);
    }

    function it_uses_decorated_data_persister_to_remove_address(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        AddressInterface $address
    ): void {
        $decoratedDataPersister->remove($address, [])->shouldBeCalled();

        $this->remove($address);
    }
}
