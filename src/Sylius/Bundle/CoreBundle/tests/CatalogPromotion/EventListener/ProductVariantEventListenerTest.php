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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\ProductVariantEventListener;
use Sylius\Component\Core\Event\ProductVariantCreated;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductVariantEventListenerTest extends TestCase
{
    private MessageBusInterface&MockObject $eventBus;

    private ProductVariantEventListener $productVariantEventListener;

    protected function setUp(): void
    {
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->productVariantEventListener = new ProductVariantEventListener($this->eventBus);
    }

    public function testDispatchesProductVariantCreatedAfterCreatingProductVariant(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($variant);
        $variant->expects($this->once())->method('getCode')->willReturn('MUG');

        $message = new ProductVariantCreated('MUG');

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message))
        ;

        $this->productVariantEventListener->dispatchProductVariantCreatedEvent($event);
    }

    public function testDispatchesProductVariantUpdatedAfterUpdatingProductVariant(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($variant);
        $variant->expects($this->once())->method('getCode')->willReturn('MUG');

        $message = new ProductVariantUpdated('MUG');

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message))
        ;

        $this->productVariantEventListener->dispatchProductVariantUpdatedEvent($event);
    }

    public function testThrowsExceptionIfEventObjectIsNotAProductVariant(): void
    {
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn('badObject');

        $this->expectException(InvalidArgumentException::class);

        $this->productVariantEventListener->dispatchProductVariantUpdatedEvent($event);
    }
}
