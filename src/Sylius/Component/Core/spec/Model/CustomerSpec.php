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

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;

final class CustomerSpec extends ObjectBehavior
{
    function it_implements_a_user_component_interface(): void
    {
        $this->shouldImplement(CustomerInterface::class);
    }

    function it_has_no_billing_address_by_default(): void
    {
        $this->getDefaultAddress()->shouldReturn(null);
    }

    function its_addresses_is_collection(): void
    {
        $this->getAddresses()->shouldHaveType(ArrayCollection::class);
    }

    function it_has_no_addresses_by_default(): void
    {
        $this->getAddresses()->count()->shouldReturn(0);
    }

    function its_billing_address_is_mutable(AddressInterface $address): void
    {
        $this->setDefaultAddress($address);
        $this->getDefaultAddress()->shouldReturn($address);
    }

    function its_addresses_are_mutable(AddressInterface $address): void
    {
        $this->addAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }

    function it_can_remove_addresses(AddressInterface $address): void
    {
        $this->addAddress($address);
        $this->removeAddress($address);
        $this->hasAddress($address)->shouldReturn(false);
    }

    function it_adds_address_when_billing_address_is_set(AddressInterface $address): void
    {
        $this->setDefaultAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }

    function it_has_no_user_by_default(): void
    {
        $this->getUser()->shouldReturn(null);
    }

    function its_user_is_mutable(ShopUserInterface $user): void
    {
        $user->setCustomer($this)->shouldBeCalled();

        $this->setUser($user);
        $this->getUser()->shouldReturn($user);
    }

    function it_throws_an_invalid_argument_exception_when_user_is_not_a_shop_user_type(UserInterface $user): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('setUser', [$user]);
    }

    function it_resets_customer_of_previous_user(ShopUserInterface $previousUser, ShopUserInterface $user): void
    {
        $this->setUser($previousUser);

        $previousUser->setCustomer(null)->shouldBeCalled();

        $this->setUser($user);
    }

    function it_does_not_replace_user_if_it_is_already_set(ShopUserInterface $user): void
    {
        $user->setCustomer($this)->shouldBeCalledOnce();

        $this->setUser($user);
        $this->setUser($user);
    }
}
