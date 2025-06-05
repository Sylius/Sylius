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
use Sylius\Bundle\ApiBundle\EventSubscriber\ProductVariantEventSubscriber;
use Sylius\Component\Core\Event\ProductVariantCreated;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductVariantEventSubscriberTest extends TestCase
{
    private MessageBusInterface&MockObject $eventBus;

    private ProductVariantEventSubscriber $productVariantEventSubscriber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->productVariantEventSubscriber = new ProductVariantEventSubscriber($this->eventBus);
    }

    public function testDispatchesProductVariantCreatedAfterCreatingProductVariant(): void
    {
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_POST);

        $variantMock->expects(self::once())->method('getCode')->willReturn('MUG');

        $message = new ProductVariantCreated('MUG');

        $this->eventBus->expects(self::once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message));

        $this->productVariantEventSubscriber->postWrite(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $variantMock,
        ));
    }

    public function testDispatchesProductVariantUpdatedAfterWritingProductVariant(): void
    {
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_PUT);

        $variantMock->expects(self::once())->method('getCode')->willReturn('MUG');

        $message = new ProductVariantUpdated('MUG');

        $this->eventBus->expects(self::once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message));

        $this->productVariantEventSubscriber->postWrite(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $variantMock,
        ));
    }

    public function testDoesNothingAfterWritingOtherEntity(): void
    {
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_PUT);

        $this->eventBus->expects(self::never())->method('dispatch');

        $this->productVariantEventSubscriber->postWrite(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            new \stdClass(),
        ));
    }
}
