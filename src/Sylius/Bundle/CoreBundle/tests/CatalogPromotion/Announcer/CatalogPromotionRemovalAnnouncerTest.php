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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncer;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\DisableCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionRemovalAnnouncerTest extends TestCase
{
    private MessageBusInterface&MockObject $commandBus;

    private CatalogPromotionRemovalAnnouncer $catalogPromotionRemovalAnnouncer;

    protected function setUp(): void
    {
        $this->commandBus = $this->createMock(MessageBusInterface::class);
        $this->catalogPromotionRemovalAnnouncer = new CatalogPromotionRemovalAnnouncer($this->commandBus);
    }

    public function testImplementsCatalogPromotionRemovalAnnouncerInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionRemovalAnnouncerInterface::class, $this->catalogPromotionRemovalAnnouncer);
    }

    public function testDispatchesRemoveCatalogPromotionCommandOnEnabledCatalogPromotion(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $catalogPromotion->method('getCode')->willReturn('CATALOG_PROMOTION_CODE');
        $catalogPromotion->method('isEnabled')->willReturn(true);

        $dispatchedMessages = [];

        $this->commandBus
            ->expects($this->exactly(3))
            ->method('dispatch')
            ->willReturnCallback(function ($message) use (&$dispatchedMessages) {
                $dispatchedMessages[] = $message;

                return new Envelope($message);
            })
        ;

        $this->catalogPromotionRemovalAnnouncer->dispatchCatalogPromotionRemoval($catalogPromotion);

        $this->assertCount(3, $dispatchedMessages);

        $this->assertInstanceOf(UpdateCatalogPromotionState::class, $dispatchedMessages[0]);
        $this->assertInstanceOf(DisableCatalogPromotion::class, $dispatchedMessages[1]);
        $this->assertInstanceOf(RemoveCatalogPromotion::class, $dispatchedMessages[2]);
    }

    public function testDispatchesRemoveCatalogPromotionCommandOnDisabledCatalogPromotion(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $catalogPromotion->method('getCode')->willReturn('CATALOG_PROMOTION_CODE');
        $catalogPromotion->method('isEnabled')->willReturn(false);

        $dispatchedMessages = [];

        $this->commandBus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function ($message) use (&$dispatchedMessages) {
                $dispatchedMessages[] = $message;

                return new Envelope($message);
            })
        ;

        $this->catalogPromotionRemovalAnnouncer->dispatchCatalogPromotionRemoval($catalogPromotion);

        $this->assertCount(2, $dispatchedMessages);

        $this->assertInstanceOf(UpdateCatalogPromotionState::class, $dispatchedMessages[0]);
        $this->assertInstanceOf(RemoveCatalogPromotion::class, $dispatchedMessages[1]);

        foreach ($dispatchedMessages as $message) {
            $this->assertNotInstanceOf(DisableCatalogPromotion::class, $message, 'DisableCatalogPromotion should not be dispatched.');
        }
    }
}
