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

namespace Tests\Sylius\Bundle\ApiBundle\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\EventSubscriber\AttributeEventSubscriber;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class AttributeEventSubscriberTest extends TestCase
{
    private MockObject&ServiceRegistryInterface $registry;

    private AttributeEventSubscriber $attributeEventSubscriber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = $this->createMock(ServiceRegistryInterface::class);
        $this->attributeEventSubscriber = new AttributeEventSubscriber($this->registry);
    }

    public function testImplementsEventSubscriberInterface(): void
    {
        self::assertInstanceOf(EventSubscriberInterface::class, $this->attributeEventSubscriber);
    }

    public function testDoesNothingWhenControllerResultIsNotAnAttribute(): void
    {
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects(self::once())->method('getMethod');

        $this->registry->expects(self::never())->method('has');

        $this->attributeEventSubscriber->assignStorageType(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            new \stdClass(),
        ));
    }

    public function testDoesNothingWhenAttributeHasNoType(): void
    {
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var AttributeInterface|MockObject $attributeMock */
        $attributeMock = $this->createMock(AttributeInterface::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_POST);

        $attributeMock->expects(self::once())->method('getType')->willReturn(null);

        $this->registry->expects(self::never())->method('has');

        $this->attributeEventSubscriber->assignStorageType(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $attributeMock,
        ));
    }

    public function testDoesNothingWhenAttributeHasAStorageType(): void
    {
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var AttributeInterface|MockObject $attributeMock */
        $attributeMock = $this->createMock(AttributeInterface::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_POST);

        $attributeMock->expects(self::atLeastOnce())->method('getType')->willReturn('text');

        $attributeMock->expects(self::once())->method('getStorageType')->willReturn('text');

        $this->registry->expects(self::never())->method('has');

        $this->attributeEventSubscriber->assignStorageType(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $attributeMock,
        ));
    }

    public function testDoesNothingWhenAttributeTypeIsNotRegistered(): void
    {
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var AttributeInterface|MockObject $attributeMock */
        $attributeMock = $this->createMock(AttributeInterface::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_POST);

        $attributeMock->expects(self::atLeastOnce())->method('getType')->willReturn('foo');

        $attributeMock->expects(self::once())->method('getStorageType')->willReturn(null);

        $this->registry->expects(self::once())->method('has')->with('foo')->willReturn(false);

        $this->attributeEventSubscriber->assignStorageType(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $attributeMock,
        ));
    }

    public function testSetsStorageTypeBasedOnSetAttributeType(): void
    {
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var AttributeInterface|MockObject $attributeMock */
        $attributeMock = $this->createMock(AttributeInterface::class);
        /** @var AttributeTypeInterface|MockObject $attributeTypeMock */
        $attributeTypeMock = $this->createMock(AttributeTypeInterface::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_POST);

        $attributeMock->expects(self::atLeastOnce())->method('getType')->willReturn('foo');

        $attributeMock->expects(self::once())->method('getStorageType')->willReturn(null);

        $this->registry->expects(self::once())->method('has')->with('foo')->willReturn(true);

        $this->registry->expects(self::once())->method('get')->with('foo')->willReturn($attributeTypeMock);

        $attributeTypeMock->expects(self::once())->method('getStorageType')->willReturn('bar');

        $attributeMock->expects(self::once())->method('setStorageType')->with('bar');

        $this->attributeEventSubscriber->assignStorageType(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $attributeMock,
        ));
    }
}
