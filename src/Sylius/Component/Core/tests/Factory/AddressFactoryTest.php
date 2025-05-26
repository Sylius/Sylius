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
use Sylius\Component\Core\Factory\AddressFactory;
use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class AddressFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $decoratedFactory;

    private AddressInterface&MockObject $address;

    private AddressFactory $factory;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->address = $this->createMock(AddressInterface::class);
        $this->factory = new AddressFactory($this->decoratedFactory);
    }

    public function testShouldImplementAddressFactoryInterfacet_implements_address_factory_interface(): void
    {
        $this->assertInstanceOf(AddressFactoryInterface::class, $this->factory);
    }

    public function testShouldBeResourceFactory(): void
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->factory);
    }

    public function testShouldCreateNewAddress(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->address);

        $this->assertSame($this->address, $this->factory->createNew());
    }

    public function testShouldCreateNewAddressWithCustomer(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->address);
        $this->address->expects($this->once())->method('setCustomer')->with($customer);

        $this->assertSame($this->address, $this->factory->createForCustomer($customer));
    }
}
