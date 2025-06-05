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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\CatalogPromotionEventListener;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CatalogPromotionEventListenerTest extends TestCase
{
    private CatalogPromotionAnnouncerInterface&MockObject $catalogPromotionAnnouncer;

    private CatalogPromotionEventListener $catalogPromotionEventListener;

    protected function setUp(): void
    {
        $this->catalogPromotionAnnouncer = $this->createMock(CatalogPromotionAnnouncerInterface::class);
        $this->catalogPromotionEventListener = new CatalogPromotionEventListener($this->catalogPromotionAnnouncer);
    }

    public function testUsesAnnouncerToDispatchCatalogPromotionCreatedEventAfterCreatingCatalogPromotion(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($catalogPromotion);

        $this->catalogPromotionAnnouncer
            ->expects($this->once())
            ->method('dispatchCatalogPromotionCreatedEvent')
            ->with($catalogPromotion)
        ;

        $this->catalogPromotionEventListener->handleCatalogPromotionCreatedEvent($event);
    }

    public function testUsesAnnouncerToDispatchCatalogPromotionUpdatedEventAfterUpdatingCatalogPromotion(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($catalogPromotion);

        $this->catalogPromotionAnnouncer
            ->expects($this->once())
            ->method('dispatchCatalogPromotionUpdatedEvent')
            ->with($catalogPromotion)
        ;

        $this->catalogPromotionEventListener->handleCatalogPromotionUpdatedEvent($event);
    }

    public function testThrowsAnExceptionIfEventObjectIsNotACatalogPromotion(): void
    {
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn('badObject');

        $this->expectException(InvalidArgumentException::class);

        $this->catalogPromotionEventListener->handleCatalogPromotionUpdatedEvent($event);
    }
}
