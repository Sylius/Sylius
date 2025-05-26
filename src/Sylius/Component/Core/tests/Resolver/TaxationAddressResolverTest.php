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

namespace Tests\Sylius\Component\Core\Resolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Resolver\TaxationAddressResolver;
use Sylius\Component\Core\Resolver\TaxationAddressResolverInterface;

final class TaxationAddressResolverTest extends TestCase
{
    private OrderInterface $order;

    private AddressInterface&MockObject $billingAddress;

    private AddressInterface&MockObject $shippingAddress;

    private TaxationAddressResolver $resolver;

    protected function setUp(): void
    {
        $this->billingAddress = $this->createMock(AddressInterface::class);
        $this->shippingAddress = $this->createMock(AddressInterface::class);
        $this->order = new Order();
        $this->order->setShippingAddress($this->shippingAddress);
        $this->order->setBillingAddress($this->billingAddress);
        $this->resolver = new TaxationAddressResolver(false);
    }

    public function testShouldImplementTaxationAddressResolverInterface(): void
    {
        $this->assertInstanceOf(TaxationAddressResolverInterface::class, $this->resolver);
    }

    public function testShouldReturnBillingAddressFromOrderIfItHasDefaultParameter(): void
    {
        $this->assertSame($this->billingAddress, $this->resolver->getTaxationAddressFromOrder($this->order));
        $this->assertNotSame($this->shippingAddress, $this->resolver->getTaxationAddressFromOrder($this->order));
    }

    public function testShouldReturnShippingAddressFromOrderIfParameterIsTrue(): void
    {
        $this->resolver = new TaxationAddressResolver(true);

        $this->assertSame($this->shippingAddress, $this->resolver->getTaxationAddressFromOrder($this->order));
        $this->assertNotSame($this->billingAddress, $this->resolver->getTaxationAddressFromOrder($this->order));
    }
}
