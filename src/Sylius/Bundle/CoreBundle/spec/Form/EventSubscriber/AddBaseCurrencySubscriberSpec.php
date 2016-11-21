<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddBaseCurrencySubscriber;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class AddBaseCurrencySubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AddBaseCurrencySubscriber::class);
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData']);
    }
    
    function it_sets_base_currency_as_disabled_when_channel_is_not_new(
        FormEvent $event, 
        ChannelInterface $channel, 
        FormInterface $form
    ) {
        $event->getData()->willReturn($channel);
        $event->getForm()->willReturn($form);

        $channel->getId()->willReturn(2);

        $form
            ->add('baseCurrency', Argument::type('string'), Argument::withEntry('disabled', true))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_does_not_set_base_currency_as_enabled_when_channel_is_new(
        FormEvent $event,
        ChannelInterface $channel,
        FormInterface $form
    ) {
        $event->getData()->willReturn($channel);
        $event->getForm()->willReturn($form);

        $channel->getId()->willReturn(null);

        $form
            ->add('baseCurrency', Argument::type('string'), Argument::withEntry('disabled', false))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }
    
    function it_throws_unexpected_type_exception_when_resource_does_not_implements_channel_interface(
        FormEvent $event,
        $resource
    ) {
        $event->getData()->willReturn($resource);
        $this->shouldThrow(UnexpectedTypeException::class)->during('preSetData', [$event]);
    }
}
