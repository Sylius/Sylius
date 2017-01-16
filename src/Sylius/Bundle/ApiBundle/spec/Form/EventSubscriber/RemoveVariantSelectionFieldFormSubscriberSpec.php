<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ApiBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Form\EventSubscriber\RemoveVariantSelectionFieldFormSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class RemoveVariantSelectionFieldFormSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RemoveVariantSelectionFieldFormSubscriber::class);
    }

    function it_is_event_subscriber_instance()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_removes_variant_selection_method_field_if_selection_method_is_not_passed(
        FormEvent $event,
        Form $form
    ) {
        $event->getData()->willReturn([]);
        $event->getForm()->willReturn($form);
        
        $form->remove('variantSelectionMethod')->shouldBeCalled();
        
        $this->preSubmit($event);
    }

    function it_does_nothing_if_variant_selection_method_is_passed(
        FormEvent $event,
        Form $form
    ) {
        $event->getData()->willReturn(['variantSelectionMethod' => 'match']);
        $event->getForm()->willReturn($form);

        $form->remove('variantSelectionMethod')->shouldNotBeCalled();

        $this->preSubmit($event);
    }
}
