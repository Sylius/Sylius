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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\ChannelDeletionListener;
use Sylius\Component\Channel\Checker\ChannelDeletionCheckerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class ChannelDeletionListenerTest extends TestCase
{
    private ChannelDeletionCheckerInterface&MockObject $channelDeletionChecker;

    private MockObject&ShippingMethodRepositoryInterface $shippingMethodRepository;

    private EntityManagerInterface&MockObject $entityManager;

    private ChannelDeletionListener $channelDeletionListener;

    protected function setUp(): void
    {
        $this->channelDeletionChecker = $this->createMock(ChannelDeletionCheckerInterface::class);
        $this->shippingMethodRepository = $this->createMock(ShippingMethodRepositoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->channelDeletionListener = new ChannelDeletionListener(
            $this->channelDeletionChecker,
            $this->shippingMethodRepository,
            $this->entityManager,
        );
    }

    public function testThrowsAnExceptionWhenSubjectIsNotAChannelOnPreDelete(): void
    {
        $event = $this->createEventWithSubject('invalid_subject');

        $this->expectException(InvalidArgumentException::class);

        $this->channelDeletionListener->onChannelPreDelete($event);
    }

    public function testAllowsChannelDeletionWhenChannelIsDeletable(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $event = $this->createEventWithSubject($channel);

        $this->channelDeletionChecker
            ->expects(self::once())
            ->method('isDeletable')
            ->with($channel)
            ->willReturn(true)
        ;

        $event->expects(self::never())->method('stop');

        $this->channelDeletionListener->onChannelPreDelete($event);
    }

    public function testPreventsChannelDeletionWhenChannelIsNotDeletable(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $event = $this->createEventWithSubject($channel);

        $this->channelDeletionChecker
            ->expects(self::once())
            ->method('isDeletable')
            ->with($channel)
            ->willReturn(false)
        ;

        $event->expects(self::once())->method('stop')->with('sylius.channel.delete_error');

        $this->channelDeletionListener->onChannelPreDelete($event);
    }

    public function testThrowsAnExceptionWhenSubjectIsNotAChannelOnPostDelete(): void
    {
        $event = $this->createEventWithSubject('invalid_subject');

        $this->expectException(InvalidArgumentException::class);

        $this->channelDeletionListener->removeChannelConfigurationFromShippingMethods($event);
    }

    public function testDoesNotRemoveConfigurationWhenChannelHasNoCode(): void
    {
        $channel = $this->createChannelWithCode(null);
        $event = $this->createEventWithSubject($channel);

        $this->shippingMethodRepository->expects(self::never())->method('findByChannelCodeInConfiguration');
        $this->entityManager->expects(self::never())->method('flush');

        $this->channelDeletionListener->removeChannelConfigurationFromShippingMethods($event);
    }

    public function testRemovesChannelCodeFromShippingMethodConfiguration(): void
    {
        $channel = $this->createChannelWithCode('DELETED_CHANNEL');
        $event = $this->createEventWithSubject($channel);

        $shippingMethod1 = $this->createShippingMethodWithConfiguration([
            'DELETED_CHANNEL' => ['amount' => 100],
            'OTHER_CHANNEL' => ['amount' => 200],
        ]);
        $shippingMethod2 = $this->createShippingMethodWithConfiguration([
            'DELETED_CHANNEL' => ['amount' => 300],
        ]);

        $this->shippingMethodRepository
            ->expects(self::once())
            ->method('findByChannelCodeInConfiguration')
            ->with('DELETED_CHANNEL')
            ->willReturn([$shippingMethod1, $shippingMethod2])
        ;

        $shippingMethod1
            ->expects(self::once())
            ->method('setConfiguration')
            ->with(['OTHER_CHANNEL' => ['amount' => 200]])
        ;

        $shippingMethod2
            ->expects(self::once())
            ->method('setConfiguration')
            ->with([])
        ;

        $this->entityManager->expects(self::once())->method('flush');

        $this->channelDeletionListener->removeChannelConfigurationFromShippingMethods($event);
    }

    public function testFlushesChangesEvenWhenNoShippingMethodsFound(): void
    {
        $channel = $this->createChannelWithCode('DELETED_CHANNEL');
        $event = $this->createEventWithSubject($channel);

        $this->shippingMethodRepository
            ->expects(self::once())
            ->method('findByChannelCodeInConfiguration')
            ->with('DELETED_CHANNEL')
            ->willReturn([])
        ;

        $this->entityManager->expects(self::once())->method('flush');

        $this->channelDeletionListener->removeChannelConfigurationFromShippingMethods($event);
    }

    private function createEventWithSubject(mixed $subject): GenericEvent&MockObject
    {
        $event = $this->createMock(GenericEvent::class);
        $event->expects(self::once())->method('getSubject')->willReturn($subject);

        return $event;
    }

    private function createChannelWithCode(?string $code): ChannelInterface&MockObject
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->expects(self::once())->method('getCode')->willReturn($code);

        return $channel;
    }

    /** @param array<string, mixed> $configuration */
    private function createShippingMethodWithConfiguration(array $configuration): MockObject&ShippingMethodInterface
    {
        $shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $shippingMethod->expects(self::once())->method('getConfiguration')->willReturn($configuration);

        return $shippingMethod;
    }
}
