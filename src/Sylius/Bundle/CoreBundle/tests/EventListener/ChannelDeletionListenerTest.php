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

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\ChannelDeletionListener;
use Sylius\Component\Channel\Checker\ChannelDeletionCheckerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\ShippingMethod\Updater\ChannelAwareShippingMethodUpdaterInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

#[AllowMockObjectsWithoutExpectations]
final class ChannelDeletionListenerTest extends TestCase
{
    private ChannelDeletionCheckerInterface&MockObject $channelDeletionChecker;

    private ChannelAwareShippingMethodUpdaterInterface&MockObject $shippingMethodUpdater;

    private ChannelDeletionListener $channelDeletionListener;

    protected function setUp(): void
    {
        $this->channelDeletionChecker = $this->createMock(ChannelDeletionCheckerInterface::class);
        $this->shippingMethodUpdater = $this->createMock(ChannelAwareShippingMethodUpdaterInterface::class);
        $this->channelDeletionListener = new ChannelDeletionListener(
            $this->channelDeletionChecker,
            $this->shippingMethodUpdater,
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

        $this->shippingMethodUpdater
            ->expects(self::never())
            ->method('removeChannelConfigurationFromShippingMethods')
        ;

        $this->channelDeletionListener->removeChannelConfigurationFromShippingMethods($event);
    }

    public function testRemovesChannelCodeFromShippingMethodConfiguration(): void
    {
        $channel = $this->createChannelWithCode('DELETED_CHANNEL');
        $event = $this->createEventWithSubject($channel);

        $this->shippingMethodUpdater
            ->expects(self::once())
            ->method('removeChannelConfigurationFromShippingMethods')
            ->with('DELETED_CHANNEL')
        ;

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
}
