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
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddBaseCurrencySubscriber;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class AddBaseCurrencySubscriberTest extends TestCase
{
    private AddBaseCurrencySubscriber $subscriber;

    protected function setUp(): void
    {
        $this->subscriber = new AddBaseCurrencySubscriber();
    }

    public function testImplementsEventSubscriberInterface(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testSubscribesToEvent(): void
    {
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber::getSubscribedEvents());
        $this->assertSame('preSetData', $this->subscriber::getSubscribedEvents()[FormEvents::PRE_SET_DATA]);
    }

    public function testDisablesBaseCurrencyForExistingChannelWithBaseCurrency(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $form = $this->createMock(FormInterface::class);
        $event = $this->createMock(FormEvent::class);

        $event->method('getData')->willReturn($channel);
        $event->method('getForm')->willReturn($form);
        $channel->method('getId')->willReturn(2);
        $channel->method('getBaseCurrency')->willReturn($currency);

        $form
            ->expects($this->once())
            ->method('add')
            ->with(
                'baseCurrency',
                $this->anything(),
                $this->callback(fn (array $options) => $options['disabled'] === true),
            )->willReturn($form)
        ;

        $this->subscriber->preSetData($event);
    }

    public function testDoesNotDisableBaseCurrencyForNewChannelsWithBaseCurrency(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $form = $this->createMock(FormInterface::class);
        $event = $this->createMock(FormEvent::class);

        $event->method('getData')->willReturn($channel);
        $event->method('getForm')->willReturn($form);
        $channel->method('getId')->willReturn(null);
        $channel->method('getBaseCurrency')->willReturn($currency);

        $form
            ->expects($this->once())
            ->method('add')
            ->with(
                'baseCurrency',
                $this->anything(),
                $this->callback(fn (array $options) => $options['disabled'] === false),
            )->willReturn($form)
        ;

        $this->subscriber->preSetData($event);
    }

    public function testDoesNotDisableBaseCurrencyForExistingChannelsWithoutBaseCurrency(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $form = $this->createMock(FormInterface::class);
        $event = $this->createMock(FormEvent::class);

        $event->method('getData')->willReturn($channel);
        $event->method('getForm')->willReturn($form);
        $channel->method('getId')->willReturn(1);
        $channel->method('getBaseCurrency')->willReturn(null);

        $form
            ->expects($this->once())
            ->method('add')
            ->with(
                'baseCurrency',
                $this->anything(),
                $this->callback(fn (array $options) => $options['disabled'] === false),
            )->willReturn($form)
        ;

        $this->subscriber->preSetData($event);
    }

    public function testDoesNotDisableBaseCurrencyForNewChannelWithoutBaseCurrency(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $form = $this->createMock(FormInterface::class);
        $event = $this->createMock(FormEvent::class);

        $event->method('getData')->willReturn($channel);
        $event->method('getForm')->willReturn($form);
        $channel->method('getId')->willReturn(null);
        $channel->method('getBaseCurrency')->willReturn(null);

        $form
            ->expects($this->once())
            ->method('add')
            ->with(
                'baseCurrency',
                $this->anything(),
                $this->callback(fn (array $options) => $options['disabled'] === false),
            )->willReturn($form)
        ;

        $this->subscriber->preSetData($event);
    }

    public function testThrowsExceptionWhenEventDataIsNotAChannel(): void
    {
        $event = $this->createMock(FormEvent::class);
        $event->method('getData')->willReturn(new \stdClass());

        $this->expectException(UnexpectedTypeException::class);
        $this->subscriber->preSetData($event);
    }
}
