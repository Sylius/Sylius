<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class AddressDataPersisterSpec extends ObjectBehavior
{
    function let(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        UserContextInterface $userContext,
    ): void {
        $this->beConstructedWith($decoratedDataPersister, $userContext);
    }

    function it_supports_only_address_entity(AddressInterface $address, ResourceInterface $resource): void
    {
        $this->supports($address)->shouldReturn(true);
        $this->supports($resource)->shouldReturn(false);
    }

    function it_sets_a_customer_and_marks_an_address_as_default_during_persisting_an_address(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressInterface $address,
    ): void {
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $customer->getDefaultAddress()->willReturn(null);

        $address->setCustomer($customer)->shouldBeCalled();
        $customer->setDefaultAddress($address)->shouldBeCalled();

        $decoratedDataPersister->persist($address, [])->shouldBeCalled();

        $this->persist($address);
    }

    function it_sets_a_customer_without_marking_an_address_as_default_during_persisting_an_address(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressInterface $address,
        AddressInterface $defaultAddress,
    ): void {
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $customer->getDefaultAddress()->willReturn($defaultAddress);

        $address->setCustomer($customer)->shouldBeCalled();
        $customer->setDefaultAddress($address)->shouldNotBeCalled();

        $decoratedDataPersister->persist($address, [])->shouldBeCalled();

        $this->persist($address);
    }

    function it_does_not_set_a_customer_if_there_is_not_logged_in_customer(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        AddressInterface $address,
    ): void {
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn(null);

        $address->setCustomer(Argument::any())->shouldNotBeCalled();

        $decoratedDataPersister->persist($address, [])->shouldBeCalled();

        $this->persist($address);
    }

    function it_uses_decorated_data_persister_to_remove_address(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        AddressInterface $address,
    ): void {
        $userContext->getUser()->willReturn($shopUser);

        $decoratedDataPersister->remove($address, [])->shouldBeCalled();

        $this->remove($address);
    }

    function it_throws_an_exception_if_there_is_not_logged_in_user_during_persisting(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        AddressInterface $address,
    ): void {
        $decoratedDataPersister->persist($address, [])->shouldNotBeCalled();

        $this->shouldThrow(MissingTokenException::class)->during('persist', [$address]);
    }

    function it_throws_an_exception_if_there_is_not_logged_in_user_during_removing(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        AddressInterface $address,
    ): void {
        $decoratedDataPersister->remove($address, [])->shouldNotBeCalled();

        $this->shouldThrow(MissingTokenException::class)->during('remove', [$address]);
    }
}
