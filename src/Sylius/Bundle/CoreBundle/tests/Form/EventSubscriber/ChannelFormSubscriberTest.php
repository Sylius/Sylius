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

namespace Tests\Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\ChannelFormSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ChannelFormSubscriberTest extends TestCase
{
    private ChannelFormSubscriber $channelFormSubscriber;

    protected function setUp(): void
    {
        $this->channelFormSubscriber = new ChannelFormSubscriber();
    }

    public function testAnEventSubscriberInstance(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->channelFormSubscriber);
    }

    public function testListensOnPreSubmitDataEvent(): void
    {
        $this->assertSame([
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ], $this->channelFormSubscriber->getSubscribedEvents());
    }

    public function testAddsABaseCurrencyToCurrenciesWhenItIsNotThere(): void
    {
        $event = $this->createMock(FormEvent::class);

        $event->expects($this->once())->method('getData')
            ->willReturn([
                'defaultLocale' => 'en_US',
                'locales' => ['en_US'],
                'baseCurrency' => 'USD',
            ])
        ;

        $event->expects($this->once())->method('setData')->with([
            'defaultLocale' => 'en_US',
            'locales' => ['en_US'],
            'baseCurrency' => 'USD',
            'currencies' => ['USD'],
        ])
        ;

        $this->channelFormSubscriber->preSubmit($event);
    }

    public function testAppendsABaseCurrencyToCurrenciesWhenItIsNotThere(): void
    {
        $event = $this->createMock(FormEvent::class);

        $event->expects($this->once())->method('getData')
            ->willReturn([
                'defaultLocale' => 'en_US',
                'locales' => ['en_US'],
                'baseCurrency' => 'USD',
                'currencies' => ['GBP'],
            ])
        ;

        $event->expects($this->once())->method('setData')->with([
            'defaultLocale' => 'en_US',
            'locales' => ['en_US'],
            'baseCurrency' => 'USD',
            'currencies' => ['GBP', 'USD'],
        ])
        ;

        $this->channelFormSubscriber->preSubmit($event);
    }

    public function testAddsADefaultLocaleToLocalesWhenItIsNotThere(): void
    {
        $event = $this->createMock(FormEvent::class);

        $event->expects($this->once())->method('getData')
            ->willReturn([
                'defaultLocale' => 'en_US',
                'baseCurrency' => 'USD',
                'currencies' => ['USD'],
            ])
        ;

        $event->expects($this->once())->method('setData')->with([
            'defaultLocale' => 'en_US',
            'locales' => ['en_US'],
            'baseCurrency' => 'USD',
            'currencies' => ['USD'],
        ])
        ;

        $this->channelFormSubscriber->preSubmit($event);
    }

    public function testAppendsADefaultLocaleToLocalesWhenItIsNotThere(): void
    {
        $event = $this->createMock(FormEvent::class);

        $event->expects($this->once())->method('getData')
            ->willReturn([
                'defaultLocale' => 'en_US',
                'locales' => ['de_DE'],
                'baseCurrency' => 'USD',
                'currencies' => ['USD'],
            ])
        ;

        $event->expects($this->once())->method('setData')->with([
            'defaultLocale' => 'en_US',
            'locales' => ['de_DE', 'en_US'],
            'baseCurrency' => 'USD',
            'currencies' => ['USD'],
        ])
        ;

        $this->channelFormSubscriber->preSubmit($event);
    }

    public function testAddsADefaultLocaleAndABaseCurrencyWhenTheyAreNotThere(): void
    {
        $event = $this->createMock(FormEvent::class);

        $event->expects($this->once())->method('getData')
            ->willReturn([
                'defaultLocale' => 'en_US',
                'baseCurrency' => 'USD',
            ])
        ;

        $event->expects($this->once())->method('setData')->with([
            'defaultLocale' => 'en_US',
            'locales' => ['en_US'],
            'baseCurrency' => 'USD',
            'currencies' => ['USD'],
        ])
        ;

        $this->channelFormSubscriber->preSubmit($event);
    }
}
