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
use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncer;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class CatalogPromotionAnnouncerTest extends TestCase
{
    private MessageBusInterface&MockObject $eventBus;

    private DelayStampCalculatorInterface&MockObject $delayStampCalculator;

    private ClockInterface&MockObject $clock;

    private CatalogPromotionAnnouncer $catalogPromotionAnnouncer;

    protected function setUp(): void
    {
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->delayStampCalculator = $this->createMock(DelayStampCalculatorInterface::class);
        $this->clock = $this->createMock(ClockInterface::class);
        $this->catalogPromotionAnnouncer = new CatalogPromotionAnnouncer($this->eventBus, $this->delayStampCalculator, $this->clock);
    }

    public function testImplementsCatalogPromotionAnnouncerInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionAnnouncerInterface::class, $this->catalogPromotionAnnouncer);
    }

    public function testDispatchesCatalogPromotionCreatedAndEndedEvents(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = new \DateTime('2021-10-11');

        $now = new \DateTimeImmutable();

        $this->clock->method('now')->willReturn($now);

        $catalogPromotion->method('getCode')->willReturn('SALE');
        $catalogPromotion->method('getStartDate')->willReturn($startDateTime);
        $catalogPromotion->method('getEndDate')->willReturn($endDateTime);

        $startDelayStamp = new DelayStamp(200000);
        $endDelayStamp = new DelayStamp(300000);

        $this->delayStampCalculator
            ->method('calculate')
            ->willReturnMap([
                [$now, $startDateTime, $startDelayStamp],
                [$now, $endDateTime, $endDelayStamp],
            ])
        ;

        $messageCreate = new CatalogPromotionCreated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $this->eventBus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function ($message, $stamps) use ($messageCreate, $messageEnd, $startDelayStamp, $endDelayStamp) {
                if ($message == $messageCreate) {
                    $this->assertEquals([$startDelayStamp], $stamps);

                    return new Envelope($messageCreate);
                }

                if ($message == $messageEnd) {
                    $this->assertEquals([$endDelayStamp], $stamps);

                    return new Envelope($messageEnd);
                }

                $this->fail('Unexpected message dispatched');
            })
        ;

        $this->catalogPromotionAnnouncer->dispatchCatalogPromotionCreatedEvent($catalogPromotion);
    }

    public function testDoesNotDispatchCatalogPromotionEndedWhenCatalogPromotionHasNoEndDateConfigured(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $startDateTime = new \DateTime('2021-10-10');
        $now = new \DateTimeImmutable();

        $catalogPromotion->method('getCode')->willReturn('SALE');
        $catalogPromotion->method('getStartDate')->willReturn($startDateTime);
        $catalogPromotion->method('getEndDate')->willReturn(null);

        $this->clock->method('now')->willReturn($now);

        $startDelayStamp = new DelayStamp(200000);

        $this->delayStampCalculator
            ->method('calculate')
            ->with($now, $startDateTime)
            ->willReturn($startDelayStamp)
        ;

        $messageCreate = new CatalogPromotionCreated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($message, $stamps) use ($messageCreate, $startDelayStamp, $messageEnd) {
                if ($message == $messageCreate) {
                    $this->assertEquals([$startDelayStamp], $stamps);

                    return new Envelope($messageCreate);
                }

                if ($message == $messageEnd) {
                    $this->fail('CatalogPromotionEnded should not be dispatched when end date is null.');
                }

                $this->fail('Unexpected message dispatched.');
            })
        ;

        $this->catalogPromotionAnnouncer->dispatchCatalogPromotionCreatedEvent($catalogPromotion);
    }

    public function testDispatchesCatalogPromotionUpdatedAndCatalogPromotionEndedEvents(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = new \DateTime('2021-10-11');
        $now = new \DateTimeImmutable();

        $catalogPromotion->method('getCode')->willReturn('SALE');
        $catalogPromotion->method('getStartDate')->willReturn($startDateTime);
        $catalogPromotion->method('getEndDate')->willReturn($endDateTime);

        $this->clock->method('now')->willReturn($now);

        $startDelayStamp = new DelayStamp(200000);
        $endDelayStamp = new DelayStamp(300000);

        $this->delayStampCalculator
            ->method('calculate')
            ->willReturnMap([
                [$now, $startDateTime, $startDelayStamp],
                [$now, $endDateTime, $endDelayStamp],
            ])
        ;

        $messageUpdate = new CatalogPromotionUpdated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $this->eventBus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function ($message, $stamps) use ($messageUpdate, $messageEnd, $startDelayStamp, $endDelayStamp) {
                if ($message == $messageUpdate) {
                    $this->assertEquals([$startDelayStamp], $stamps);

                    return new Envelope($messageUpdate);
                }

                if ($message == $messageEnd) {
                    $this->assertEquals([$endDelayStamp], $stamps);

                    return new Envelope($messageEnd);
                }

                $this->fail('Unexpected message dispatched');
            })
        ;

        $this->catalogPromotionAnnouncer->dispatchCatalogPromotionUpdatedEvent($catalogPromotion);
    }

    public function testDispatchesCatalogPromotionUpdatedTwiceIfCatalogPromotionIsUpdatedWithDelayedStart(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $now = new \DateTimeImmutable('2021-10-09');
        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = new \DateTime('2021-10-11');

        $catalogPromotion->method('getCode')->willReturn('SALE');
        $catalogPromotion->method('getStartDate')->willReturn($startDateTime);
        $catalogPromotion->method('getEndDate')->willReturn($endDateTime);

        $this->clock->method('now')->willReturn($now);

        $startDelayStamp = new DelayStamp(200000);
        $endDelayStamp = new DelayStamp(300000);

        $this->delayStampCalculator
            ->method('calculate')
            ->willReturnMap([
                [$now, $startDateTime, $startDelayStamp],
                [$now, $endDateTime, $endDelayStamp],
            ])
        ;

        $this->eventBus
            ->expects($this->exactly(3))
            ->method('dispatch')
            ->willReturnCallback(function ($message, $stamps) use ($startDelayStamp, $endDelayStamp) {
                if ($message instanceof CatalogPromotionUpdated && empty($stamps)) {
                    return new Envelope($message);
                }

                if ($message instanceof CatalogPromotionUpdated && $stamps === [$startDelayStamp]) {
                    return new Envelope($message);
                }

                if ($message instanceof CatalogPromotionEnded && $stamps === [$endDelayStamp]) {
                    return new Envelope($message);
                }

                $this->fail('Unexpected message dispatched');
            })
        ;

        $this->catalogPromotionAnnouncer->dispatchCatalogPromotionUpdatedEvent($catalogPromotion);
    }
}
