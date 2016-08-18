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
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class AddPaymentMethodsFormSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddPaymentMethodsFormSubscriber');
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_listens_on_pre_set_data_event()
    {
        $this->getSubscribedEvents()->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData']);
    }

    function it_adds_payment_methods_choice_to_the_form(
        FormEvent $event,
        FormInterface $form,
        PaymentInterface $payment
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($payment);

        $form
            ->add('method', Argument::type('string'), [
                'label' => 'sylius.form.checkout.payment_method',
                'subject' => $payment,
                'expanded' => true,
            ])
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }
}
