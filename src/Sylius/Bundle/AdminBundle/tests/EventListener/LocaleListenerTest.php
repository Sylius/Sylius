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

namespace Tests\Sylius\Bundle\AdminBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\EventListener\LocaleListener;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;

final class LocaleListenerTest extends TestCase
{
    private LocaleUsageCheckerInterface&MockObject $localeUsageCheckerMock;

    private LocaleListener $localeListener;

    protected function setUp(): void
    {
        $this->localeUsageCheckerMock = $this->createMock(LocaleUsageCheckerInterface::class);
        $this->localeListener = new LocaleListener($this->localeUsageCheckerMock);
    }

    public function testDoesNothingIfLocaleIsNotUsed(): void
    {
        $locale = $this->createMock(LocaleInterface::class);
        $event = $this->createMock(GenericEvent::class);

        $this->localeUsageCheckerMock
            ->expects($this->once())
            ->method('isUsed')
            ->with('en_US')
            ->willReturn(false)
        ;

        $locale->expects($this->once())->method('getCode')->willReturn('en_US');

        $event->expects($this->once())->method('getSubject')->willReturn($locale);
        $event->expects($this->never())->method('stop');

        $this->localeListener->preDelete($event);
    }

    public function testStopsEventIfLocaleIsUsed(): void
    {
        $locale = $this->createMock(LocaleInterface::class);
        $event = $this->createMock(GenericEvent::class);

        $this->localeUsageCheckerMock
            ->expects($this->once())
            ->method('isUsed')
            ->with('en_US')
            ->willReturn(true)
        ;

        $locale->expects($this->once())->method('getCode')->willReturn('en_US');

        $event->expects($this->once())->method('getSubject')->willReturn($locale);
        $event
            ->expects($this->once())
            ->method('stop')
            ->with('sylius.locale.delete.is_used', 'error', [], Response::HTTP_UNPROCESSABLE_ENTITY)
        ;

        $this->localeListener->preDelete($event);
    }
}
