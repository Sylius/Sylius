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

namespace Tests\Sylius\Component\Core\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Factory\CustomerAfterCheckoutFactory;
use Sylius\Component\Core\Factory\CustomerAfterCheckoutFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class CustomerAfterCheckoutFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $baseFactory;

    private CustomerInterface&MockObject $customer;

    private CustomerAfterCheckoutFactory $factory;

    protected function setUp(): void
    {
        $this->baseFactory = $this->createMock(FactoryInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->factory = new CustomerAfterCheckoutFactory($this->baseFactory);
    }

    public function testShouldImplementCustomerAfterCheckoutFactoryInterface(): void
    {
        $this->assertInstanceOf(CustomerAfterCheckoutFactoryInterface::class, $this->factory);
    }

    public function testShouldBeResourceFactory(): void
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->factory);
    }

    public function testShouldCreateNewCustomer(): void
    {
        $this->baseFactory->expects($this->once())->method('createNew')->willReturn($this->customer);

        $this->assertSame($this->customer, $this->factory->createNew());
    }

    public function testShouldCreateNewCustomerAfterCheckout(): void
    {
        $guest = $this->createMock(CustomerInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $address = $this->createMock(AddressInterface::class);

        $order->expects($this->once())->method('getCustomer')->willReturn($guest);
        $order->expects($this->once())->method('getBillingAddress')->willReturn($address);
        $guest->expects($this->once())->method('getEmail')->willReturn('johndoe@example.com');
        $address->expects($this->once())->method('getFirstName')->willReturn('John');
        $address->expects($this->once())->method('getLastName')->willReturn('Doe');
        $address->expects($this->once())->method('getPhoneNumber')->willReturn('666777888');
        $this->baseFactory->expects($this->once())->method('createNew')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('setEmail')->with('johndoe@example.com');
        $this->customer->expects($this->once())->method('setFirstName')->with('John');
        $this->customer->expects($this->once())->method('setLastName')->with('Doe');
        $this->customer->expects($this->once())->method('setPhoneNumber')->with('666777888');

        $this->assertSame($this->customer, $this->factory->createAfterCheckout($order));
    }
}
