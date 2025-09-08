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

namespace Tests\Sylius\Component\Shipping\Resolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolver;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;

final class DefaultShippingMethodResolverTest extends TestCase
{
    /** @var ShippingMethodRepositoryInterface<ShippingMethodInterface>&MockObject */
    private MockObject&ShippingMethodRepositoryInterface $shippingMethodRepository;

    private MockObject&ShippingMethodInterface $firstShippingMethod;

    private MockObject&ShippingMethodInterface $secondShippingMethod;

    private MockObject&ShipmentInterface $shipment;

    private DefaultShippingMethodResolver $resolver;

    protected function setUp(): void
    {
        $this->shippingMethodRepository = $this->createMock(ShippingMethodRepositoryInterface::class);
        $this->firstShippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->secondShippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->resolver = new DefaultShippingMethodResolver($this->shippingMethodRepository);
    }

    public function testShouldImplementDefaultShippingMethodResolverInterface(): void
    {
        $this->assertInstanceOf(DefaultShippingMethodResolverInterface::class, $this->resolver);
    }

    public function testShouldReturnFirstEnabledShippingMethodAsDefaultit_returns_first_enabled_shipping_method_as_default(): void
    {
        $this->shippingMethodRepository->expects($this->once())->method('findBy')->with(['enabled' => true])->willReturn([
           $this->firstShippingMethod,
           $this->secondShippingMethod,
        ]);

        $this->assertSame($this->firstShippingMethod, $this->resolver->getDefaultShippingMethod($this->shipment));
    }

    public function testShouldThrowExceptionIfThereIsNoEnabledShippingMethods(): void
    {
        $this->expectException(UnresolvedDefaultShippingMethodException::class);
        $this->shippingMethodRepository->expects($this->once())->method('findBy')->with(['enabled' => true])->willReturn([]);

        $this->resolver->getDefaultShippingMethod($this->shipment);
    }
}
