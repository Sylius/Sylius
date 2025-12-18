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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Listener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionStateChangedListener;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionStateChangedListenerTest extends TestCase
{
    private MessageBusInterface&MockObject $messageBus;

    private CatalogPromotionStateChangedListener $listener;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->listener = new CatalogPromotionStateChangedListener($this->messageBus);
    }

    public function testDispatchesUpdateStateCommandOfCatalogPromotionThatHasJustBeenCreated(): void
    {
        $command = new UpdateCatalogPromotionState('WINTER_MUGS_SALE');
        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn(new Envelope($command))
        ;

        ($this->listener)(new CatalogPromotionCreated('WINTER_MUGS_SALE'));
    }

    public function testDispatchesUpdateStateCommandOfCatalogPromotionThatHasJustBeenUpdated(): void
    {
        $command = new UpdateCatalogPromotionState('WINTER_MUGS_SALE');
        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn(new Envelope($command))
        ;

        ($this->listener)(new CatalogPromotionUpdated('WINTER_MUGS_SALE'));
    }

    public function testDispatchesUpdateStateCommandOfCatalogPromotionThatHasJustBeenEnded(): void
    {
        $command = new UpdateCatalogPromotionState('WINTER_MUGS_SALE');
        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn(new Envelope($command))
        ;

        ($this->listener)(new CatalogPromotionEnded('WINTER_MUGS_SALE'));
    }
}
