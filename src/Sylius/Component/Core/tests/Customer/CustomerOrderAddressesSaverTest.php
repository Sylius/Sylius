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

namespace Tests\Sylius\Component\Core\Customer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Customer\CustomerAddressAdderInterface;
use Sylius\Component\Core\Customer\CustomerOrderAddressesSaver;
use Sylius\Component\Core\Customer\OrderAddressesSaverInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class CustomerOrderAddressesSaverTest extends TestCase
{
    private CustomerAddressAdderInterface&MockObject $addressAdder;

    private MockObject&OrderInterface $order;

    private CustomerInterface&MockObject $customer;

    private MockObject&ShopUserInterface $user;

    private CustomerOrderAddressesSaver $customerOrderAddressesSaver;

    protected function setUp(): void
    {
        $this->addressAdder = $this->createMock(CustomerAddressAdderInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->user = $this->createMock(ShopUserInterface::class);
        $this->customerOrderAddressesSaver = new CustomerOrderAddressesSaver($this->addressAdder);
    }

    public function testShouldImplementOrderAddressesSaverInterface(): void
    {
        $this->assertInstanceOf(OrderAddressesSaverInterface::class, $this->customerOrderAddressesSaver);
    }

    public function testShouldSaveAddressesFromGivenOrder(): void
    {
        $shippingAddress = $this->createMock(AddressInterface::class);
        $billingAddress = $this->createMock(AddressInterface::class);
        $this->order->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getUser')->willReturn($this->user);
        $this->order->expects($this->once())->method('getShippingAddress')->willReturn($shippingAddress);
        $this->order->expects($this->once())->method('getBillingAddress')->willReturn($billingAddress);
        $this->addressAdder->expects($this->exactly(2))->method('add')->with(
            $this->customer,
            $this->isInstanceOf(AddressInterface::class),
        );

        $this->customerOrderAddressesSaver->saveAddresses($this->order);
    }

    public function testShouldNotSaveAddressesForGuestOrder(): void
    {
        $this->order->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getUser')->willReturn(null);
        $this->addressAdder->expects($this->never())->method('add')->with(
            $this->customer,
            $this->isInstanceOf(AddressInterface::class),
        );

        $this->customerOrderAddressesSaver->saveAddresses($this->order);
    }

    public function testShouldNotSaveEmptyAddresses(): void
    {
        $this->order->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getUser')->willReturn($this->user);
        $this->order->expects($this->once())->method('getShippingAddress')->willReturn(null);
        $this->order->expects($this->once())->method('getBillingAddress')->willReturn(null);
        $this->addressAdder->expects($this->never())->method('add')->with(
            $this->customer,
            $this->isInstanceOf(AddressInterface::class),
        );

        $this->customerOrderAddressesSaver->saveAddresses($this->order);
    }
}
