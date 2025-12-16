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

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Core\Customer\CustomerAddressAdderInterface;
use Sylius\Component\Core\Customer\CustomerUniqueAddressAdder;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class CustomerUniqueAddressAdderTest extends TestCase
{
    private AddressComparatorInterface&MockObject $addressComparator;

    private CustomerInterface&MockObject $customer;

    private AddressInterface&MockObject $address;

    private Collection&MockObject $addresses;

    private \Iterator&MockObject $iterator;

    private CustomerUniqueAddressAdder $customerUniqueAddressAdder;

    protected function setUp(): void
    {
        $this->addressComparator = $this->createMock(AddressComparatorInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->address = $this->createMock(AddressInterface::class);
        $this->addresses = $this->createMock(Collection::class);
        $this->iterator = $this->createMock(\Iterator::class);
        $this->customerUniqueAddressAdder = new CustomerUniqueAddressAdder($this->addressComparator);
    }

    public function testShouldImplementAddressAdderInterface(): void
    {
        $this->assertInstanceOf(CustomerAddressAdderInterface::class, $this->customerUniqueAddressAdder);
    }

    public function testShouldDoNothingWhenAddressIsAlreadyPresentOnTheCustomer(): void
    {
        $this->iterator->expects($this->once())->method('rewind');
        $this->iterator->expects($this->once())->method('valid')->willReturn(true);
        $this->iterator->expects($this->once())->method('current')->willReturn($this->address);
        $this->addresses->expects($this->once())->method('getIterator')->willReturn($this->iterator);
        $this->customer->expects($this->once())->method('getAddresses')->willReturn($this->addresses);
        $this->addressComparator->expects($this->once())->method('equal')->with($this->address, $this->address)->willReturn(true);
        $this->customer->expects($this->never())->method('addAddress')->with($this->address);

        $this->customerUniqueAddressAdder->add($this->customer, $this->address);
    }

    public function testShouldAddAddressWhenNoOtherIsPresentOnTheCustomer(): void
    {
        $this->iterator->expects($this->once())->method('rewind');
        $this->iterator->expects($this->once())->method('valid')->willReturn(false);
        $this->addresses->expects($this->once())->method('getIterator')->willReturn($this->iterator);
        $this->customer->expects($this->once())->method('getAddresses')->willReturn($this->addresses);
        $this->addressComparator->expects($this->never())->method('equal')->with(
            $this->isInstanceOf(AddressInterface::class),
            $this->isInstanceOf(AddressInterface::class),
        );
        $this->customer->expects($this->once())->method('addAddress')->with($this->address);

        $this->customerUniqueAddressAdder->add($this->customer, $this->address);
    }

    public function testShouldAddAddressWhenDifferentThanTheOnePresentOnTheCustomer(): void
    {
        $newAddress = $this->createMock(AddressInterface::class);
        $this->iterator->expects($this->once())->method('rewind');
        $this->iterator->expects($this->exactly(2))->method('valid')->willReturnOnConsecutiveCalls(true, false);
        $this->iterator->expects($this->once())->method('current')->willReturn($this->address);
        $this->addresses->expects($this->once())->method('getIterator')->willReturn($this->iterator);
        $this->customer->expects($this->once())->method('getAddresses')->willReturn($this->addresses);
        $this->addressComparator->expects($this->once())->method('equal')->with($this->address, $newAddress)->willReturn(false);
        $this->customer->expects($this->once())->method('addAddress')->with($newAddress);

        $this->customerUniqueAddressAdder->add($this->customer, $this->address);
    }
}
