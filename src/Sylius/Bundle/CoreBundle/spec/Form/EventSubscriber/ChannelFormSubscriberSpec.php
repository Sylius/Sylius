<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ChannelFormSubscriberSpec extends ObjectBehavior
{
    public function it_is_an_event_subscriber_instance(): void
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    public function it_listens_on_pre_submit_data_event(): void
    {
        $this->getSubscribedEvents()->shouldReturn([
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ]);
    }

    public function it_adds_a_base_currency_to_currencies_when_it_is_not_there(FormEvent $event): void
    {
        $event
            ->getData()
            ->willReturn([
                'defaultLocale' => 'en_US',
                'locales' => ['en_US'],
                'baseCurrency' => 'USD',
            ])
        ;

        $event
            ->setData([
                'defaultLocale' => 'en_US',
                'locales' => ['en_US'],
                'baseCurrency' => 'USD',
                'currencies' => ['USD'],
            ])
            ->shouldBeCalled()
        ;

        $this->preSubmit($event);
    }

    public function it_appends_a_base_currency_to_currencies_when_it_is_not_there(FormEvent $event): void
    {
        $event
            ->getData()
            ->willReturn([
                'defaultLocale' => 'en_US',
                'locales' => ['en_US'],
                'baseCurrency' => 'USD',
                'currencies' => ['GBP'],
            ])
        ;

        $event
            ->setData([
                'defaultLocale' => 'en_US',
                'locales' => ['en_US'],
                'baseCurrency' => 'USD',
                'currencies' => ['GBP', 'USD'],
            ])
            ->shouldBeCalled()
        ;

        $this->preSubmit($event);
    }

    public function it_adds_a_default_locale_to_locales_when_it_is_not_there(FormEvent $event): void
    {
        $event
            ->getData()
            ->willReturn([
                'defaultLocale' => 'en_US',
                'baseCurrency' => 'USD',
                'currencies' => ['USD'],
            ])
        ;

        $event
            ->setData([
                'defaultLocale' => 'en_US',
                'locales' => ['en_US'],
                'baseCurrency' => 'USD',
                'currencies' => ['USD'],
            ])
            ->shouldBeCalled()
        ;

        $this->preSubmit($event);
    }

    public function it_appends_a_default_locale_to_locales_when_it_is_not_there(FormEvent $event): void
    {
        $event
            ->getData()
            ->willReturn([
                'defaultLocale' => 'en_US',
                'locales' => ['de_DE'],
                'baseCurrency' => 'USD',
                'currencies' => ['USD'],
            ])
        ;

        $event
            ->setData([
                'defaultLocale' => 'en_US',
                'locales' => ['de_DE', 'en_US'],
                'baseCurrency' => 'USD',
                'currencies' => ['USD'],
            ])
            ->shouldBeCalled()
        ;

        $this->preSubmit($event);
    }

    public function it_adds_a_default_locale_and_a_base_currency_when_they_are_not_there(FormEvent $event): void
    {
        $event
            ->getData()
            ->willReturn([
                'defaultLocale' => 'en_US',
                'baseCurrency' => 'USD',
            ])
        ;

        $event
            ->setData([
                'defaultLocale' => 'en_US',
                'locales' => ['en_US'],
                'baseCurrency' => 'USD',
                'currencies' => ['USD'],
            ])
            ->shouldBeCalled()
        ;

        $this->preSubmit($event);
    }
}
