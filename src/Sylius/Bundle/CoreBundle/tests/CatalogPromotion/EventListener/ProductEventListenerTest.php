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
use Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\ProductEventListener;
use Sylius\Component\Core\Event\ProductCreated;
use Sylius\Component\Core\Event\ProductUpdated;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductEventListenerTest extends TestCase
{
    private MessageBusInterface&MockObject $eventBus;

    private ProductEventListener $productEventListener;

    protected function setUp(): void
    {
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->productEventListener = new ProductEventListener($this->eventBus);
    }

    public function testDispatchesProductCreatedAfterCreatingProduct(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $product = $this->createMock(ProductInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($product);
        $product->expects($this->once())->method('getCode')->willReturn('MUG');

        $message = new ProductCreated('MUG');

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message))
        ;

        $this->productEventListener->dispatchProductCreatedEvent($event);
    }

    public function testDispatchesProductUpdatedAfterUpdatingProduct(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $product = $this->createMock(ProductInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($product);
        $product->expects($this->once())->method('getCode')->willReturn('MUG');

        $message = new ProductUpdated('MUG');

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message))
        ;

        $this->productEventListener->dispatchProductUpdatedEvent($event);
    }

    public function testThrowsExceptionIfEventObjectIsNotAProduct(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $event->expects($this->once())->method('getSubject')->willReturn('badObject');

        $this->expectException(InvalidArgumentException::class);

        $this->productEventListener->dispatchProductUpdatedEvent($event);
    }
}
