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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;

final class ShopUserTest extends TestCase
{
    private CustomerInterface&MockObject $customer;

    private ShopUser $shopUser;

    protected function setUp(): void
    {
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->shopUser = new ShopUser();
    }

    public function testShouldImplementUserComponentInterface(): void
    {
        $this->assertInstanceOf(ShopUserInterface::class, $this->shopUser);
    }

    public function testShouldReturnCustomerEmail(): void
    {
        $this->customer->expects($this->once())->method('getEmail')->willReturn('jon@snow.wall');
        $this->customer->expects($this->once())->method('setUser')->with($this->shopUser);

        $this->shopUser->setCustomer($this->customer);

        $this->assertSame('jon@snow.wall', $this->shopUser->getEmail());
    }

    public function testShouldEmailBeNullIfCustomerIsNotAssigned(): void
    {
        $this->assertNull($this->shopUser->getEmail());
    }

    public function testShouldSetsCustomerEmail(): void
    {
        $this->customer->expects($this->once())->method('setEmail')->with('jon@snow.wall');
        $this->customer->expects($this->once())->method('setUser')->with($this->shopUser);

        $this->shopUser->setCustomer($this->customer);
        $this->shopUser->setEmail('jon@snow.wall');
    }

    public function testShouldThrowExceptionIfNoCustomerIsAssignedWhileSettingEmail(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->shopUser->setEmail('jon@snow.wall');
    }

    public function testShouldReturnCustomerEmailCanonical(): void
    {
        $this->customer->expects($this->once())->method('getEmailCanonical')->willReturn('jon@snow.wall');
        $this->customer->expects($this->once())->method('setUser')->with($this->shopUser);

        $this->shopUser->setCustomer($this->customer);

        $this->assertSame('jon@snow.wall', $this->shopUser->getEmailCanonical());
    }

    public function testShouldReturnNullAsCustomerEmailCanonicalIfNoCustomerIsAssigned(): void
    {
        $this->assertNull($this->shopUser->getEmailCanonical());
    }

    public function testShouldSetCustomerEmailCanonical(): void
    {
        $this->customer->expects($this->once())->method('setEmailCanonical')->with('jon@snow.wall');
        $this->customer->expects($this->once())->method('setUser')->with($this->shopUser);

        $this->shopUser->setCustomer($this->customer);
        $this->shopUser->setEmailCanonical('jon@snow.wall');
    }

    public function testShouldThrowExceptionIfNoUserIsAssignedWhileSettingEmailCanonical(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->shopUser->setEmailCanonical('jon@snow.wall');
    }
}
