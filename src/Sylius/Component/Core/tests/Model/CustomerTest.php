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

namespace Tests\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;

final class CustomerTest extends TestCase
{
    private AddressInterface&MockObject $address;

    private MockObject&ShopUserInterface $user;

    private Customer $customer;

    protected function setUp(): void
    {
        $this->address = $this->createMock(AddressInterface::class);
        $this->user = $this->createMock(ShopUserInterface::class);
        $this->customer = new Customer();
    }

    public function testShouldImplementUserComponentInterface(): void
    {
        $this->assertInstanceOf(CustomerInterface::class, $this->customer);
    }

    public function testShouldNotHaveBillingAddressByDefault(): void
    {
        $this->assertNull($this->customer->getDefaultAddress());
    }

    public function testShouldInitializeAddressesCollection(): void
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->customer->getAddresses());
    }

    public function testShouldNotHaveAddressesByDefault(): void
    {
        $this->assertTrue($this->customer->getAddresses()->isEmpty());
    }

    public function testShouldBillingAddressBeMutable(): void
    {
        $this->customer->setDefaultAddress($this->address);

        $this->assertSame($this->address, $this->customer->getDefaultAddress());
    }

    public function testShouldAddAddress(): void
    {
        $this->customer->addAddress($this->address);

        $this->assertTrue($this->customer->hasAddress($this->address));
    }

    public function testShouldRemoveAddress(): void
    {
        $this->customer->addAddress($this->address);

        $this->customer->removeAddress($this->address);

        $this->assertFalse($this->customer->hasAddress($this->address));
    }

    public function testShouldAddAddressWhenBillingAddressIsSet(): void
    {
        $this->customer->setDefaultAddress($this->address);

        $this->assertTrue($this->customer->hasAddress($this->address));
    }

    public function testShouldNotHaveUserByDefault(): void
    {
        $this->assertNull($this->customer->getUser());
    }

    public function testShouldUserBeMutable(): void
    {
        $this->user->expects($this->once())->method('setCustomer')->with($this->customer);

        $this->customer->setUser($this->user);

        $this->assertSame($this->user, $this->customer->getUser());
    }

    public function testShouldThrowInvalidArgumentExceptionWhenUserIsNotShopUserType(): void
    {
        $notShopUser = $this->createMock(UserInterface::class);

        $this->expectException(\InvalidArgumentException::class);

        $this->customer->setUser($notShopUser);
    }

    public function testShouldResetCustomerOfPreviousUser(): void
    {
        $previousUser = $this->createMock(ShopUserInterface::class);
        $this->customer->setUser($previousUser);
        $previousUser->expects($this->once())->method('setCustomer')->with(null);

        $this->customer->setUser($this->user);
    }

    public function testShouldNotReplaceUserIfItIsAlreadySet(): void
    {
        $this->user->expects($this->once())->method('setCustomer')->with($this->customer);

        $this->customer->setUser($this->user);
        $this->customer->setUser($this->user);
    }
}
