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
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class AddressDataPersisterSpec extends ObjectBehavior
{
    function let(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage
    ): void {
        $this->beConstructedWith($decoratedDataPersister, $tokenStorage);
    }

    function it_supports_only_address_entity(AddressInterface $address, ProductInterface $product): void
    {
        $this->supports($address)->shouldReturn(true);
        $this->supports($product)->shouldReturn(false);
    }

    function it_sets_a_customer_and_marks_an_address_as_default_during_persisting_an_address(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressInterface $address
    ): void {
        $decoratedDataPersister->persist($address, [])->shouldBeCalled();

        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $customer->getDefaultAddress()->willReturn(null);

        $address->setCustomer($customer)->shouldBeCalled();
        $customer->setDefaultAddress($address)->shouldBeCalled();

        $this->persist($address);
    }

    function it_sets_a_customer_without_marking_an_address_as_default_during_persisting_an_address(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressInterface $address,
        AddressInterface $defaultAddress
    ): void {
        $decoratedDataPersister->persist($address, [])->shouldBeCalled();

        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $customer->getDefaultAddress()->willReturn($defaultAddress);

        $address->setCustomer($customer)->shouldBeCalled();
        $customer->setDefaultAddress($address)->shouldNotBeCalled();

        $this->persist($address);
    }

    function it_does_not_set_a_customer_if_logged_in_user_is_not_shop_user(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $adminUser,
        CustomerInterface $customer,
        AddressInterface $address
    ): void {
        $decoratedDataPersister->persist($address, [])->shouldBeCalled();

        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($adminUser);

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
