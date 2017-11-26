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

final class CustomerSpec extends ObjectBehavior
{
    public function it_implements_a_user_component_interface(): void
    {
        $this->shouldImplement(CustomerInterface::class);
    }

    public function it_has_no_billing_address_by_default(): void
    {
        $this->getDefaultAddress()->shouldReturn(null);
    }

    public function its_addresses_is_collection(): void
    {
        $this->getAddresses()->shouldHaveType(ArrayCollection::class);
    }

    public function it_has_no_addresses_by_default(): void
    {
        $this->getAddresses()->count()->shouldReturn(0);
    }

    public function its_billing_address_is_mutable(AddressInterface $address): void
    {
        $this->setDefaultAddress($address);
        $this->getDefaultAddress()->shouldReturn($address);
    }

    public function its_addresses_are_mutable(AddressInterface $address): void
    {
        $this->addAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }

    public function it_can_remove_addresses(AddressInterface $address): void
    {
        $this->addAddress($address);
        $this->removeAddress($address);
        $this->hasAddress($address)->shouldReturn(false);
    }

    public function it_adds_address_when_billing_address_is_set(AddressInterface $address): void
    {
        $this->setDefaultAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }
}
