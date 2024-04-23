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

namespace spec\Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class AddBaseCurrencySubscriberSpec extends ObjectBehavior
{
    function it_implements_event_subscriber_interface(): void
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event(): void
    {
        $this::getSubscribedEvents()->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData']);
    }

    function it_disables_base_currency_for_existing_channel_with_base_currency(
        FormEvent $event,
        FormInterface $form,
        ChannelInterface $channel,
        CurrencyInterface $currency,
    ): void {
        $event->getData()->willReturn($channel);
        $event->getForm()->willReturn($form);

        $channel->getId()->willReturn(2);
        $channel->getBaseCurrency()->willReturn($currency);

        $form
            ->add('baseCurrency', Argument::type('string'), Argument::withEntry('disabled', true))
            ->willReturn($form)
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_does_not_disable_base_currency_for_new_channels_with_base_currency(
        FormEvent $event,
        FormInterface $form,
        ChannelInterface $channel,
        CurrencyInterface $currency,
    ): void {
        $event->getData()->willReturn($channel);
        $event->getForm()->willReturn($form);

        $channel->getId()->willReturn(null);
        $channel->getBaseCurrency()->willReturn($currency);

        $form
            ->add('baseCurrency', Argument::type('string'), Argument::withEntry('disabled', false))
            ->willReturn($form)
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_does_not_disable_base_currency_for_existing_channels_without_base_currency(
        FormEvent $event,
        FormInterface $form,
        ChannelInterface $channel,
    ): void {
        $event->getData()->willReturn($channel);
        $event->getForm()->willReturn($form);

        $channel->getId()->willReturn(1);
        $channel->getBaseCurrency()->willReturn(null);

        $form
            ->add('baseCurrency', Argument::type('string'), Argument::withEntry('disabled', false))
            ->willReturn($form)
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_does_not_disable_base_currency_for_new_channel_without_base_currency(
        FormEvent $event,
        FormInterface $form,
        ChannelInterface $channel,
    ): void {
        $event->getData()->willReturn($channel);
        $event->getForm()->willReturn($form);

        $channel->getId()->willReturn(null);
        $channel->getBaseCurrency()->willReturn(null);

        $form
            ->add('baseCurrency', Argument::type('string'), Argument::withEntry('disabled', false))
            ->willReturn($form)
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_throws_exception_when_event_data_is_not_a_channel(FormEvent $event): void
    {
        $event->getData()->willReturn(new \stdClass());

        $this->shouldThrow(UnexpectedTypeException::class)->during('preSetData', [$event]);
    }
}
